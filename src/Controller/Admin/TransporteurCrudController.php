<?php

namespace App\Controller\Admin;

use App\Entity\Transporteur;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class TransporteurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Transporteur::class;
    }

   
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('titre'),
            TextareaField::new('description'),
            MoneyField::new('prix')->setCurrency('EUR')
        ];
    }
    
}
