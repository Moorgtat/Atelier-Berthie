<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('titre', 'Titre'),
            TextareaField::new('content', 'Contenu'),
            TextField::new('url', 'Url'),
            ImageField::new('image', 'Image')->setBasePath('uploads/')->setFormTypeOptions(['mapped' => false, 'required' => false]),
        ];
    }
    
}
