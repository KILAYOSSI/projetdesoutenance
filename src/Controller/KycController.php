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
        
        if ($user->canSell()) {
            return $this->redirectToRoute('vendeur_dashboard');
        }

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
            $photoPiece = $form->get('photoPieceRecto')->getData();

            if ($photoPiece) {
                $kyc->setPhotoPieceRecto($this->uploadFile($photoPiece, 'kyc'));
            }

            $kyc->setUtilisateur($user);
            $kyc->setStatus(Kyc::STATUS_PENDING);
            
            $this->entityManager->persist($kyc);
            $this->entityManager->flush();

            $user->setIsVendeur(true);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre demande de vérification a été soumise avec succès.');
            return $this->redirectToRoute('kyc_status');
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

        $action = $request->query->get('action');

        if ($action === 'validate') {
            $kyc->setStatus(Kyc::STATUS_VALIDATED);
            $user = $kyc->getUtilisateur();
            $user->setIsKycValidated(true);
            $this->entityManager->flush();
            $this->addFlash('success', 'La demande KYC a été validée.');
        } elseif ($action === 'reject') {
            $kyc->setStatus(Kyc::STATUS_REJECTED);
            $user = $kyc->getUtilisateur();
            $user->setIsVendeur(false);
            $user->setIsKycValidated(false);
            $this->entityManager->flush();
            $this->addFlash('warning', 'La demande KYC a été rejetée.');
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
}
