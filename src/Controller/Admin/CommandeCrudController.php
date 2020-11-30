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
        $updatePreparation = Action::new('updatePreparation', 'Commencer préparation', 'fas fa-box-open')->linkToCrudAction('updatePreparation')->displayIf(static function ($entity) {
            return $entity->getState() == 1;
        });
        $updateLivraison = Action::new('updateLivraison', 'Confirmer livraison', 'fas fa-truck')->linkToCrudAction('updateLivraison')->displayIf(static function ($entity) {
            return $entity->getState() == 2;
        });
        $updateSuivi = Action::new('updateSuivi', 'Renvoyer le numéro de suivi', 'fas fa-truck')->linkToCrudAction('updateSuivi')->displayIf(static function ($entity) {
            return $entity->getState() == 3;
        });

        return $actions
            ->add('detail', $updatePreparation)
            ->add('detail', $updateLivraison)
            ->add('detail', $updateSuivi)
            ->add('index', 'detail')
            ->remove('index', 'edit')
            ->remove('index', 'new');
    }

    public function updatePreparation(AdminContext $context)
    {
        $commande = $context->getEntity()->getInstance();
        $commande->setState(2);
        $this->entityManager->flush();

        $this->addFlash('success', "La commande".$commande->getReference()." est bien en cours de préparation.");
        
        $url = $this->crudUrlGenerator->build()
            ->setController(CommandeCrudController::class)
            ->setAction('detail')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function updateLivraison(AdminContext $context)
    {
        $commande = $context->getEntity()->getInstance();
        $commande->setState(3);
        $this->entityManager->flush();

        if ($commande->getTransporteurTitre() == "Colissimo")
        {
            //TO DO Envoi mail envie numéro de suivi
            $this->addFlash('success', "<div class='text-center'>Un mail de confirmation d'éxpédition a été envoyé pour la commande <strong>".$commande->getReference()."</strong> avec le numero de suivi: ".$commande->getSuivi().".</div>");
        }
        elseif ($commande->getTransporteurTitre() == "Retrait Magasin")
        {
            //TO DO Envoi mail Commande retirée en magasin
            $this->addFlash('success', "<div class='text-center'>Un mail de confirmation de retrait en magasin a été envoyé pour la commande <strong>".$commande->getReference()."</strong>.</div>");
        }

        $url = $this->crudUrlGenerator->build()
            ->setController(CommandeCrudController::class)
            ->setAction('detail')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function updateSuivi(AdminContext $context)
    {
        $commande = $context->getEntity()->getInstance();

        //TO DO Envoi mail envie numéro de suivi
        $this->addFlash('success', "<div class='text-center'>Un mail avec le numero de suivi <strong>".$commande->getSuivi()."</strong> pour la commande <strong>".$commande->getReference()." </strong>a bien été renvoyé.</div>");
        
        $url = $this->crudUrlGenerator->build()
            ->setController(CommandeCrudController::class)
            ->setAction('detail')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('stringCreatedAt', 'Passée le'),
            TextField::new('user.getFullName', 'Utilisateur'),
            ArrayField::new('commandeDetails', 'Produits achetés')->setFormTypeOptions(['mapped' => false, 'required' => false])->hideOnIndex(),
            TextEditorField::new('livraison', 'Adresse de livraison')->onlyOnDetail(),
            TextField::new('suivi', 'Numéro de suivi'),
            MoneyField::new('total', 'Total')->setCurrency('EUR')->setFormTypeOptions(['mapped' => false, 'required' => false]),
            MoneyField::new('transporteurPrix', 'Frais de port')->setCurrency('EUR'),
            TextField::new('transporteurTitre', 'Transporteur'),
            ChoiceField::new('state')->setChoices([
                'Commande non confirmée' => 0,
                'Commande à traiter' => 1,
                'Préparation en cours' => 2,
                'Commande finalisée' => 3,
                'Paiement commande refusé' => 5
            ])
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
