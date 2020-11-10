<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeErreurController extends AbstractController
{
    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="commande_erreur")
     */
    public function erreur(CommandeRepository $commandeRepo, $stripeSessionId): Response
    {
        $commande = $commandeRepo->findOneByStripeSessionId($stripeSessionId);
        
        if(!$commande || $commande->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }
        
        return $this->render('commande/erreur.html.twig', [
            'commande' => $commande
        ]);
    }
}
