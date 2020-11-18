<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('new_password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => "Votre mot de passe et le mot de passe de confirmation doivent être identique.",
            'required' => true,
            'first_options' => ['label' => 'Mon nouveau mot de passe', 'attr' => [
                'placeholder' => 'Saisissez votre nouveau mot de passe...'
                ]
            ],
            'second_options' => [ 'label' => 'Confirmez votre nouveau mot de passe', 'attr' => [
                'placeholder' => 'Confirmez votre nouveau mot de passe...'
                ]
            ]
        ])
        ->add('submit', SubmitType::class, [
            'label' => "Réinitialiser"
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
