<?php

namespace App\Service;

use App\Entity\Paiement;
use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class FedapayService
{
    private $entityManager;
    private $urlGenerator;
    
    // Clés API Fedapay - À remplacer par vos vraies clés
    private const API_KEY = 'votre_cle_publique'; // Clé publique
    private const API_SECRET = 'votre_cle_secrete'; // Clé secrète
    private const MODE = 'sandbox'; // 'sandbox' pour les tests, 'live' pour la production
    
    private $client;
    
    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->client = HttpClient::create();
    }
    
    /**
     * Crée une transaction de paiement Fedapay via API
     */
    public function createPayment(Commande $commande, string $telephone): array
    {
        try {
            // Créer l'enregistrement de paiement
            $paiement = new Paiement();
            $paiement->setUtilisateur($commande->getUtilisateur());
            $paiement->setCommande($commande);
            $paiement->setMontant($commande->getMontantTotal());
            $paiement->setMethode('mobile_money');
            $paiement->setStatut('en_attente');
            $paiement->setReference('CMD-' . $commande->getId() . '-' . time());
            
            $this->entityManager->persist($paiement);
            $this->entityManager->flush();
            
            // URL de retour après paiement
            $returnUrl = $this->urlGenerator->generate('paiement_retour', [
                'id' => $paiement->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            
            // Préparer les données de la transaction
            $transactionData = [
                'amount' => (int) $commande->getMontantTotal(),
                'currency' => 'XOF',
                'description' => 'Paiement commande #' . $commande->getId() . ' - KilysAgri',
                'callback_url' => $returnUrl,
                'return_url' => $returnUrl,
                'customer' => [
                    'email' => $commande->getUtilisateur()->getEmail() ?: 'client@kilysagri.com',
                    'firstname' => $this->getFirstName($commande->getUtilisateur()->getNomComplet()),
                    'lastname' => $this->getLastName($commande->getUtilisateur()->getNomComplet()),
                    'phone_number' => [
                        'number' => $telephone,
                        'country' => 'BJ'
                    ]
                ],
                'metadata' => [
                    'paiement_id' => $paiement->getId(),
                    'commande_id' => $commande->getId()
                ]
            ];
            
            // Appeler l'API Fedapay pour créer la transaction
            $response = $this->client->request('POST', $this->getApiUrl() . '/transactions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . self::API_SECRET,
                    'Content-Type' => 'application/json',
                ],
                'json' => $transactionData,
            ]);
            
            $data = $response->toArray();
            
            if (isset($data['token'])) {
                // Stocker l'ID de transaction
                $paiement->setTransactionId($data['id'] ?? null);
                $this->entityManager->flush();
                
                // Retourner l'URL de paiement Fedapay
                return [
                    'success' => true,
                    'token' => $data['token']['id'],
                    'paiement_id' => $paiement->getId(),
                    'url' => $data['token']['payment_url']
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Erreur lors de la création du paiement'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Vérifie le statut d'un paiement
     */
    public function checkPaymentStatus(int $paiementId): array
    {
        try {
            $paiement = $this->entityManager->getRepository(Paiement::class)->find($paiementId);
            
            if (!$paiement) {
                return ['success' => false, 'error' => 'Paiement non trouvé'];
            }
            
            if ($paiement->getTransactionId()) {
                // Appeler l'API Fedapay pour vérifier le statut
                $response = $this->client->request('GET', $this->getApiUrl() . '/transactions/' . $paiement->getTransactionId(), [
                    'headers' => [
                        'Authorization' => 'Bearer ' . self::API_SECRET,
                    ],
                ]);
                
                $data = $response->toArray();
                
                // Mettre à jour le statut
                $this->updatePaiementStatus($paiement, $data['status'] ?? 'pending');
                
                return [
                    'success' => true,
                    'statut' => $paiement->getStatut()
                ];
            }
            
            return [
                'success' => false,
                'error' => 'ID de transaction manquant'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Met à jour le statut du paiement
     */
    public function updatePaiementStatus(Paiement $paiement, string $fedapayStatus): void
    {
        switch ($fedapayStatus) {
            case 'approved':
                $paiement->setStatut('succes');
                $paiement->setDatePaiement(new \DateTime());
                
                // Confirmer la commande
                $commande = $paiement->getCommande();
                if ($commande) {
                    $commande->setStatus(Commande::STATUS_CONFIRMED);
                }
                break;
            case 'declined':
            case 'cancelled':
                $paiement->setStatut('echoue');
                break;
            default:
                $paiement->setStatut('en_attente');
        }
        
        $paiement->setUpdatedAt(new \DateTime());
        $this->entityManager->flush();
    }
    
    /**
     * Callback pour traiter le paiement (webhook)
     */
    public function handleCallback(array $data): void
    {
        if (isset($data['metadata']['paiement_id'])) {
            $paiement = $this->entityManager->getRepository(Paiement::class)->find($data['metadata']['paiement_id']);
            
            if ($paiement && $paiement->getStatut() === 'en_attente') {
                $paiement->setTransactionId($data['id'] ?? null);
                $this->updatePaiementStatus($paiement, $data['status'] ?? 'pending');
            }
        }
    }
    
    /**
     * Retourne l'URL de l'API Fedapay selon le mode
     */
    private function getApiUrl(): string
    {
        return self::MODE === 'sandbox' 
            ? 'https://sandbox-api.fedapay.com/v1'
            : 'https://api.fedapay.com/v1';
    }
    
    private function getFirstName(?string $nomComplet): string
    {
        if (!$nomComplet) {
            return 'Client';
        }
        $parts = explode(' ', $nomComplet);
        return $parts[0];
    }
    
    private function getLastName(?string $nomComplet): string
    {
        if (!$nomComplet) {
            return 'KilysAgri';
        }
        $parts = explode(' ', $nomComplet);
        return count($parts) > 1 ? end($parts) : 'KilysAgri';
    }
}

