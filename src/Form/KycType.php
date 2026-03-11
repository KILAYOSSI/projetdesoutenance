<?php

namespace App\Form;

use App\Entity\Kyc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class KycType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typePiece', ChoiceType::class, [
                'label' => 'Type de pièce d\'identité',
                'choices' => [
                    'Carte Nationale d\'Identité (CNI)' => Kyc::TYPE_CNI,
                    'Passeport' => Kyc::TYPE_PASSEPORT,
                    'Permis de conduire' => Kyc::TYPE_PERMIS,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un type de pièce']),
                ],
            ])
            ->add('numeroPiece', TextType::class, [
                'label' => 'Numéro de la pièce',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le numéro de votre pièce d\'identité',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le numéro de la pièce']),
                ],
            ])
            ->add('photoPieceRecto', FileType::class, [
                'label' => 'Photo de la pièce (Recto)',
                'mapped' => false,
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez télécharger la photo de votre pièce']),
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF)',
                    ]),
                ],
            ])
            ->add('photoPieceVerso', FileType::class, [
                'label' => 'Photo de la pièce (Verso)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF)',
                    ]),
                ],
            ])
            // Photo Selfie - Champ caché qui sera rempli par la caméra
            ->add('photoSelfie', HiddenType::class, [
                'mapped' => false,
                'required' => true,
                'attr' => [
                    'id' => 'photoSelfie',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez prendre une photo selfie avec la caméra']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Kyc::class,
        ]);
    }
}

