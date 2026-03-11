<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class VendeurStep2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomBoutique', TextType::class, [
                'label' => 'Nom de votre boutique',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Fermes Kilys, Bio Benin, etc.',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le nom de votre boutique']),
                ],
            ])
            ->add('descriptionBoutique', TextareaType::class, [
                'label' => 'À propos de votre boutique',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Décrivez votre boutique, vos produits, votre philosophie...',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez décrire votre boutique']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}

