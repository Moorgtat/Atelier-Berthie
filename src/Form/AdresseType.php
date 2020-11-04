<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Nommez votre adresse',
                'attr' => [
                    'placeholder' => 'Mon domicile'
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Indiquez votre prénom',
                'attr' => [
                    'placeholder' => 'Mon prénom'
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Indiquez votre nom',
                'attr' => [
                    'placeholder' => 'Mon nom'
                ]
            ])
            ->add('societe', TextType::class, [
                'label' => 'Indiquez le nom de votre société',
                'attr' => [
                    'placeholder' => 'Ma societé'
                ]
            ])
            ->add('numero', TextType::class, [
                'label' => 'Indiquez votre numero de rue',
                'attr' => [
                    'placeholder' => 'Mon numéro de rue'
                ]
            ])
            ->add('rue', TextType::class, [
                'label' => 'Indiquez le nom de votre rue',
                'attr' => [
                    'placeholder' => 'Mon nom de rue'
                ]
            ])
            ->add('codepostal', TextType::class, [
                'label' => 'Indiquez votre code postal',
                'attr' => [
                    'placeholder' => 'Mon code postal'
                ]
            ])
            ->add('ville', TextType::class, [
                'label' => 'Indiquer votre ville',
                'attr' => [
                    'placeholder' => 'Ma ville'
                ]
            ])
            ->add('pays', CountryType::class, [
                'label' => 'Indiquez votre pays',
                'attr' => [
                    'placeholder' => 'Mon pays'
                ]
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Indiquez votre numero de téléphone',
                'attr' => [
                    'placeholder' => 'Mon téléphone'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Adresse::class,
        ]);
    }
}
