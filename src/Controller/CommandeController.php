<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Commande;
use App\Form\CommandeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    /**
     * @Route("/commande/livraison", name="commande_livraison")
     */
    public function livraison(Cart $cart, Request $request): Response
    {
        if(!$this->getUser()->getAdresses()->getValues()) {
            return $this->redirectToRoute('adresse_add');
        }

        $form = $this->createForm(CommandeType::class, null, [
            'user' => $this->getUser()
        ]);

        return $this->render('commande/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }

    /**
     * @Route("/commande/validation", name="commande_validation")
     */
    public function add(Cart $cart, Request $request): Response
    {
        $form = $this->createForm(CommandeType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime();
            $transporteur = $form->get('transporteurs')->getData();
            $delivery = $form->get('adresses')->getData();
            dd($delivery);

            $commande = new Commande();
            $commande->setUser($this->getUser());
            $commande->setCreatedAt($date);
            $commande->setTransporteurTitre($transporteur->getTitre());
            $commande->setTransporteurPrix($transporteur->getPrix());
        }

        return $this->render('commande/add.html.twig', [
            'cart' => $cart->getFull()
        ]);
    }
}
