<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
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
            TextField::new('titre')->onlyWhenCreating(),
            TextField::new('soustitre'),
            SlugField::new('slug')->setTargetFieldName('titre')->onlyWhenCreating(),
            ImageField::new('couverture')->setBasePath('uploads/')->setFormTypeOptions(['mapped' => false, 'required' => false]),
            ImageField::new('image_1')->setBasePath('uploads/')->setFormTypeOptions(['mapped' => false, 'required' => false]),
            ImageField::new('image_2')->setBasePath('uploads/')->setFormTypeOptions(['mapped' => false, 'required' => false]),
            ImageField::new('image_3')->setBasePath('uploads/')->setFormTypeOptions(['mapped' => false, 'required' => false]),
            TextareaField::new('description'),
            MoneyField::new('prix')->setCurrency('EUR'),
            AssociationField::new('categorie')
        ];
    }

}
