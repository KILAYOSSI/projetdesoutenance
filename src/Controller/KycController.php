<?php

namespace App\Controller;

use App\Entity\Kyc;
use App\Form\KycType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Annotation\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/kyc')]
class KycController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SluggerInterface $slugger;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    #[Route('/submit', name: 'kyc_submit')]
    #[IsGranted('ROLE_USER')]
    public function submit(Request $request): Response
    {
        $user = $this->getUser();
        
        // Check if user can already sell
        if ($user->canSell()) {
            return $this->redirectToRoute('vendeur_dashboard');
        }

        // Check for pending KYC
        $pendingKyc = $this->entityManager->getRepository(Kyc::class)->findOneBy([
            'utilisateur' => $user,
            'status' => Kyc::STATUS_PENDING
        ]);

        if ($pendingKyc) {
            $this->addFlash('warning', 'Vous avez déjà une demande de vérification en cours.');
            return $this->redirectToRoute('kyc_status');
        }

        $kyc = new Kyc();
        $form = $this->createForm(KycType::class, $kyc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get data from the form
            $kyc->setTypePiece($form->get('typePiece')->getData());
            $kyc->setNumeroPiece($form->get('numeroPiece')->getData());
            
            // Handle photoPieceRecto (from file upload)
            $photoPieceRectoFile = $form->get('photoPieceRecto')->getData();
            if ($photoPieceRectoFile instanceof UploadedFile) {
                $photoRectoPath = $this->uploadFile($photoPieceRectoFile, 'kyc');
                $kyc->setPhotoPieceRecto($photoRectoPath);
            }

            // Handle photoPieceVerso (from file upload)
            $photoPieceVersoFile = $form->get('photoPieceVerso')->getData();
            if ($photoPieceVersoFile instanceof UploadedFile) {
                $photoVersoPath = $this->uploadFile($photoPieceVersoFile, 'kyc');
                $kyc->setPhotoPieceVerso($photoVersoPath);
            }

            // Handle photoSelfie (base64 from camera) - now from form field
            $photoSelfieData = $form->get('photoSelfie')->getData();
            if ($photoSelfieData && strpos($photoSelfieData, 'data:image') === 0) {
                $photoSelfiePath = $this->uploadBase64Image($photoSelfieData, 'kyc');
                $kyc->setPhotoSelfie($photoSelfiePath);
            }

            // Check if we have at least the required photos
            if ($kyc->getPhotoPieceRecto() && $kyc->getPhotoSelfie()) {
                // Set the user and status
                $kyc->setUtilisateur($user);
                $kyc->setStatus(Kyc::STATUS_PENDING);
                
                // Persist the KYC
                $this->entityManager->persist($kyc);
                $this->entityManager->flush();

                // Update user as vendeur (pending validation)
                $user->setIsVendeur(true);
                $this->entityManager->flush();

                $this->addFlash('success', 'Votre demande de vérification a été soumise avec succès. Elle sera traitée sous 24h.');
                return $this->redirectToRoute('kyc_status');
            } else {
                // Missing required photos
                $this->addFlash('error', 'Veuillez fournir la photo de la pièce (recto) et le selfie.');
            }
        }

        // Show form errors if any
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
        }

        return $this->render('kyc/submit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/status', name: 'kyc_status')]
    #[IsGranted('ROLE_USER')]
    public function status(): Response
    {
        $user = $this->getUser();
        $kycs = $this->entityManager->getRepository(Kyc::class)->findBy(
            ['utilisateur' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('kyc/status.html.twig', [
            'kycs' => $kycs,
            'user' => $user,
        ]);
    }

    #[Route('/admin/list', name: 'kyc_admin_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminList(): Response
    {
        $pendingKycs = $this->entityManager->getRepository(Kyc::class)->findBy(
            ['status' => Kyc::STATUS_PENDING],
            ['createdAt' => 'ASC']
        );

        return $this->render('kyc/admin_list.html.twig', [
            'kycs' => $pendingKycs,
        ]);
    }

    #[Route('/admin/validate/{id}', name: 'kyc_admin_validate')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminValidate(Request $request, int $id): Response
    {
        $kyc = $this->entityManager->getRepository(Kyc::class)->find($id);
        
        if (!$kyc) {
            $this->addFlash('error', 'Demande KYC introuvable.');
            return $this->redirectToRoute('kyc_admin_list');
        }

        // Handle POST request (reject with motif)
        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            if ($action === 'reject') {
                $motif = $request->request->get('motif', '');
                $kyc->setStatus(Kyc::STATUS_REJECTED);
                $kyc->setMotif($motif);
                $user = $kyc->getUtilisateur();
                $user->setIsVendeur(false);
                $user->setIsKycValidated(false);
                $this->entityManager->flush();
                $this->addFlash('warning', 'La demande KYC a été rejetée.');
                return $this->redirectToRoute('kyc_admin_list');
            }
        }

        // Handle GET request (validate)
        $action = $request->query->get('action');
        if ($action === 'validate') {
            $kyc->setStatus(Kyc::STATUS_VALIDATED);
            $user = $kyc->getUtilisateur();
            $user->setIsKycValidated(true);
            $this->entityManager->flush();
            $this->addFlash('success', 'La demande KYC a été validée.');
        }

        return $this->redirectToRoute('kyc_admin_list');
    }

    #[Route('/admin/detail/{id}', name: 'kyc_admin_detail')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminDetail(int $id): Response
    {
        $kyc = $this->entityManager->getRepository(Kyc::class)->find($id);
        
        if (!$kyc) {
            $this->addFlash('error', 'Demande KYC introuvable.');
            return $this->redirectToRoute('kyc_admin_list');
        }
        
        return $this->render('kyc/admin_detail.html.twig', [
            'kyc' => $kyc,
        ]);
    }

    private function uploadFile(UploadedFile $file, string $directory): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move(
            $this->getParameter('uploads_directory') ?? $this->getParameter('kernel.project_dir') . '/public/uploads/' . $directory,
            $newFilename
        );

        return '/uploads/' . $directory . '/' . $newFilename;
    }

    private function uploadBase64Image(string $base64Data, string $directory): string
    {
        // Remove data URL prefix if present
        $base64Data = str_replace('data:image/jpeg;base64,', '', $base64Data);
        $base64Data = str_replace('data:image/png;base64,', '', $base64Data);
        $base64Data = str_replace('data:image/gif;base64,', '', $base64Data);
        
        $imageData = base64_decode($base64Data);
        
        $newFilename = 'selfie-' . uniqid() . '.jpg';
        $uploadPath = $this->getParameter('uploads_directory') ?? $this->getParameter('kernel.project_dir') . '/public/uploads/' . $directory;
        
        file_put_contents($uploadPath . '/' . $newFilename, $imageData);
        
        return '/uploads/' . $directory . '/' . $newFilename;
    }
}
