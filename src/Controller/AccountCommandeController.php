<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountCommandeController extends AbstractController
{
    /**
     * @Route("/compte/mes-commandes", name="account_commande")
     */
    public function index(CommandeRepository $commandeRepo): Response
    {
        $commandes = $commandeRepo->findSuccesCommandes($this->getUser());
        return $this->render('account/commande.html.twig', [
            'commandes' => $commandes
        ]);
    }

    /**
     * @Route("/compte/mes-commandes/{reference}", name="account_commande_show")
     */
    public function show(CommandeRepository $commandeRepo, $reference): Response
    {
        $commande = $commandeRepo->findOneByReference($reference);
        
        if(!$commande || $commande->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_commande');
        }
        
        return $this->render('account/commande_show.html.twig', [
            'commande' => $commande
        ]);
    }
}
