<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class VendeurStep3Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('localisation', TextType::class, [
                'label' => 'Où êtes-vous basé au Benin ?',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Cotonou, Porto-Novo, Abomey-Calavi, etc.',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer votre localisation']),
                ],
            ])
            ->add('devise', ChoiceType::class, [
                'label' => 'Devise de paiement',
                'choices' => [
                    'Franc CFA (XOF)' => 'XOF',
                ],
                'data' => 'XOF',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une devise']),
                ],
            ])
            ->add('typeExploitation', ChoiceType::class, [
                'label' => 'Type d\'exploitation',
                'choices' => [
                    'Maraîichage (légumes)' => 'maraichage',
                    'Élevage' => 'elevage',
                    'Cultures vivrières' => 'cultures_vivrières',
                    'Arboriculture (fruits)' => 'arboriculture',
                    'Cultures industrielles' => 'cultures_industrielles',
                    'Aquaculture' => 'aquaculture',
                    'Apiculture' => 'apiculture',
                    'Autre' => 'autre',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner le type d\'exploitation']),
                ],
            ])
            ->add('localisationExploitation', TextType::class, [
                'label' => 'Localisation de l\'exploitation',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ville/Commune où se trouve votre exploitation',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer la localisation de votre exploitation']),
                ],
            ])
            ->add('anneesExperience', IntegerType::class, [
                'label' => 'Années d\'expérience en agriculture',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'placeholder' => 'Nombre d\'années',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer vos années d\'expérience']),
                    new Positive(['message' => 'Veuillez entrer un nombre positif']),
                ],
            ])
            ->add('capaciteProduction', ChoiceType::class, [
                'label' => 'Capacité de production régulière',
                'choices' => [
                    'Faible (< 50 kg/semaine)' => 'faible',
                    'Moyenne (50 - 200 kg/semaine)' => 'moyenne',
                    'Élevée (200 - 500 kg/semaine)' => 'elevee',
                    'Très élevée (> 500 kg/semaine)' => 'tres_elevee',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner votre capacité de production']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}

