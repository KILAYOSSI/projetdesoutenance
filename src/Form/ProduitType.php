<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Categorie;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Tomates fraîches',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le nom du produit']),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Décrivez votre produit (origine, qualité, etc.)',
                    'rows' => 4,
                ],
                'required' => false,
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix unitaire (FCFA)',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 500',
                ],
                'currency' => 'XOF',
                'divisor' => 1,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le prix']),
                    new Positive(['message' => 'Le prix doit être positif']),
                ],
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité disponible',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 100',
                    'min' => 0,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer la quantité']),
                ],
            ])
            ->add('categorie', null, [
                'label' => 'Catégorie',
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.nom', 'ASC');
                },
                'attr' => [
                    'class' => 'form-select',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une catégorie']),
                ],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Photo du produit',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF, WebP)',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}

