<?php

namespace App\Service;

use FedaPay\FedaPay;
use FedaPay\Transaction;
use App\Entity\Paiement;
use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FedapayService
{
    private $entityManager;
    private $urlGenerator;
    
    // Clés API Fedapay - À remplacer par vos vraies clés
    private const API_KEY = 'votre_cle_publique'; // Clé publique
    private const API_SECRET = 'votre_cle_secrete'; // Clé secrète
    private const MODE = 'sandbox'; // 'sandbox' pour les tests, 'live' pour la production
    
    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        
        // Initialiser Fedapay
        FedaPay::setApiKey(self::API_SECRET);
        FedaPay::setEnvironment(self::MODE);
    }
    
    /**
     * Crée une transaction de paiement Fedapay
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
            
            $callbackUrl = $this->urlGenerator->generate('paiement_callback', [
                'id' => $paiement->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            
            // Créer la transaction Fedapay
            $transaction = Transaction::create([
                'description' => 'Paiement commande #' . $commande->getId() . ' - KilysAgri',
                'amount' => (int) $commande->getMontantTotal(),
                'currency' => [
                    'iso' => 'XOF'
                ],
                'callback_url' => $callbackUrl,
                'return_url' => $returnUrl,
                'customer' => [
                    'email' => $commande->getUtilisateur()->getEmail(),
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
            ]);
            
            // Générer le token de paiement
            $token = $transaction->generateToken();
            
            return [
                'success' => true,
                'token' => $token->token,
                'paiement_id' => $paiement->getId(),
                'url' => $token->getPaymentUrl()
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
                $transaction = Transaction::retrieve($paiement->getTransactionId());
                
                // Mettre à jour le statut
                $this->updatePaiementStatus($paiement, $transaction->status);
                
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
     * Callback pour traiter le paiement
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

