<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('titre'),
            TextField::new('soustitre'),
            ImageField::new('couverture')->setBasePath('uploads/')->setFormTypeOptions(['mapped' => false, 'required' => false]),
            ImageField::new('image_1')->setBasePath('uploads/')->setFormTypeOptions(['mapped' => false, 'required' => false])->setTextAlign('left'),
            ImageField::new('image_2')->setBasePath('uploads/')->setFormTypeOptions(['mapped' => false, 'required' => false])->setTextAlign('left'),
            ImageField::new('image_3')->setBasePath('uploads/')->setFormTypeOptions(['mapped' => false, 'required' => false, 'trim' => true])->setTextAlign('left'),
            TextareaField::new('description'),
            BooleanField::new('isBest'),
            MoneyField::new('prix')->setCurrency('EUR'),
            AssociationField::new('categorie')->hideOnIndex()
        ];
    }

}
