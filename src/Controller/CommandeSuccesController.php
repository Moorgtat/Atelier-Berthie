<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeSuccesController extends AbstractController
{
    /**
     * @Route("/commande/succes/{stripeSessionId}", name="commande_succes")
     */
    public function succes(Cart $cart, EntityManagerInterface $manager, CommandeRepository $commandeRepo, $stripeSessionId): Response
    {
        $commande = $commandeRepo->findOneByStripeSessionId($stripeSessionId);
        
        if(!$commande || $commande->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if(!$commande->getIsPaid()) {
            $cart->remove();
            $commande->setIsPaid(1);
            $manager->flush();

            //Mail  client confirm
        }
        
        return $this->render('commande/succes.html.twig', [
            'commande' => $commande
        ]);
    }
}
