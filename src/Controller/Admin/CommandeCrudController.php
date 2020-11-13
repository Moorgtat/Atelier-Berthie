<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class CommandeCrudController extends AbstractCrudController
{
    private $entityManager;
    private $crudUrlGenerator;

    public function __construct(EntityManagerInterface $entityManager, CrudUrlGenerator $crudUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->crudUrlGenerator = $crudUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Commande::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreparation');
        $updateLivraison = Action::new('updateLivraison', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateLivraison');

        return $actions
            ->add('detail', $updatePreparation)
            ->add('detail', $updateLivraison)
            ->add('index', 'detail')
            ->remove('index', 'edit')
            ->remove('index', 'new');
    }

    public function updatePreparation(AdminContext $context)
    {
        $commande = $context->getEntity()->getInstance();
        $commande->setState(2);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color: green;'><strong>La commande".$commande->getReference()." est bien en cours de préparation.</strong></span>");
        
        $url = $this->crudUrlGenerator->build()
            ->setController(CommandeCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function updateLivraison(AdminContext $context)
    {
        $commande = $context->getEntity()->getInstance();
        $commande->setState(3);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color: green;'><strong>La commande".$commande->getReference()." est bien en cours de livraison.</strong></span>");
        
        $url = $this->crudUrlGenerator->build()
            ->setController(CommandeCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('stringCreatedAt', 'Passée le'),
            TextField::new('user.getFullName', 'Utilisateur'),
            TextEditorField::new('livraison', 'Adresse de livraison')->onlyOnDetail(),
            MoneyField::new('total', 'Total')->setCurrency('EUR'),
            TextField::new('transporteurTitre', 'Transporteur'),
            MoneyField::new('transporteurPrix', 'Frais de port')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3
            ]),
            ArrayField::new('commandeDetails', 'Produits achetés')->hideOnIndex()
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setDefaultSort([ 'id' => 'DESC'])
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes');
    }

}
