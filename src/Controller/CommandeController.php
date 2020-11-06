<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Commande;
use App\Entity\CommandeDetail;
use App\Form\CommandeType;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/commande/validation", name="commande_validation", methods={"POST"})
     */
    public function add(Cart $cart, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(CommandeType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime();
            $transporteur = $form->get('transporteurs')->getData();
            $livraison = $form->get('adresses')->getData();
            
            $livraison_content = $livraison->getPrenom().' '.$livraison->getNom();
            if($livraison->getSociete()) {
                $livraison_content .= '<br>'.$livraison->getSociete();
            }
            $livraison_content .= '<br>'.$livraison->getNumero().' '.$livraison->getRue();
            $livraison_content .= '<br>'.$livraison->getCodepostal().' '.$livraison->getVille();
            $livraison_content .= '<br>'.$livraison->getPays();

            $commande = new Commande();
            $commande->setUser($this->getUser());
            $commande->setCreatedAt($date);
            $commande->setTransporteurTitre($transporteur->getTitre());
            $commande->setTransporteurPrix($transporteur->getPrix());
            $commande->setLivraison($livraison_content);
            $commande->setIsPaid(0);

            $manager->persist($commande);

            foreach ($cart->getFull() as $produit) {
                $commandeDetail = new CommandeDetail();
                $commandeDetail->setMaCommande($commande);
                $commandeDetail->setProduit($produit['produit']->getTitre());
                $commandeDetail->setQuantite($produit['quantity']);
                $commandeDetail->setPrix($produit['produit']->getPrix());
                $commandeDetail->setTotal($produit['produit']->getPrix() * $produit['quantity']);
            
                $manager->persist($commandeDetail);
            }

            // $manager->flush();

            return $this->render('commande/add.html.twig', [
                'cart' => $cart->getFull(),
                'transporteur' => $transporteur,
                'livraison_content' => $livraison_content
            ]);
        }

        return $this->redirectToRoute('produits');
    }
}
