<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('prenom')
            ->add('nom')
            ->add('societe')
            ->add('numero')
            ->add('rue')
            ->add('codepostal')
            ->add('ville')
            ->add('pays')
            ->add('telephone')
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter une adresse'
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
