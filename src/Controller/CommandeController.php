<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Service\Cart;
use App\Service\Mail;
use App\Entity\Commande;
use App\Form\CommandeType;
use Stripe\Checkout\Session;
use App\Entity\CommandeDetail;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        return $this->render('commande/choix_livraison.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }

    /**
     * @Route("/commande/validation", name="commande_validation", methods={"POST"})
     */
    public function validate(Cart $cart, Request $request, EntityManagerInterface $manager): Response
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
            $reference = $date->format('dmY').'-'.uniqid();
            $commande->setReference($reference);
            $commande->setUser($this->getUser());
            $commande->setCreatedAt($date);
            $commande->setTransporteurTitre($transporteur->getTitre());
            $commande->setTransporteurPrix($transporteur->getPrix());
            $commande->setLivraison($livraison_content);
            $commande->setState(0);

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

            $manager->flush();

            return $this->render('commande/add.html.twig', [
                'cart' => $cart->getFull(),
                'transporteur' => $transporteur,
                'livraison_content' => $livraison_content,
                'reference' => $commande->getReference()
            ]);
        }

        return $this->redirectToRoute('produits');
    }


     /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function stripe(EntityManagerInterface $manager, CommandeRepository $commandeRepo, ProduitRepository $produitRepo, $reference): Response
    {
        $YOUR_DOMAIN = 'http://127.0.0.1:8000/';

        $liste_produit_stripe = [];

        $commande = $commandeRepo->findOneByReference($reference);

        if(!$commande) {
            new JsonResponse(['error' => 'commande']);
        }

        foreach ($commande->getCommandeDetails()->getValues() as $produit) {
            $produit_object = $produitRepo->findOneByTitre($produit->getProduit());           
            $liste_produit_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $produit->getPrix(),
                        'product_data' => [
                            'name' => $produit->getProduit(),
                            'images' => [$YOUR_DOMAIN."uploads/".$produit_object->getCouverture()],
                        ],
                    ],
                'quantity' => $produit->getQuantite(),
            ];
        }
        
            $liste_produit_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $commande->getTransporteurPrix(),
                        'product_data' => [
                            'name' => $commande->getTransporteurTitre(),
                            'images' => [$YOUR_DOMAIN],
                        ],
                    ],
                'quantity' => 1,
            ];

        Stripe::setApiKey('sk_test_51HlWG3GucqTraIY3sLTmaEBn253RapsttGz5sbEBh2w8SmA4QLyiudwT7ELRxGTs7YeuGmR6C8e3MTQgH6OlgzC000NZRNstat');
        
        $checkout_session = Session::create([
        'customer_email' => $this->getUser()->getEmail(),    
        'payment_method_types' => ['card'],
        'line_items' => [
            $liste_produit_stripe
        ],
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . 'commande/succes/{CHECKOUT_SESSION_ID}',
        'cancel_url' => $YOUR_DOMAIN . 'commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $commande->setStripeSessionId($checkout_session->id);
        $manager->flush();

        $response = new JsonResponse(['id' => $checkout_session->id]);

        return $response;
    }

     /**
     * @Route("/commande/succes/{stripeSessionId}", name="commande_succes")
     */
    public function succes(Cart $cart, EntityManagerInterface $manager, CommandeRepository $commandeRepo, $stripeSessionId): Response
    {
        $commande = $commandeRepo->findOneByStripeSessionId($stripeSessionId);
        
        if(!$commande || $commande->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if($commande->getState() == 0) {
            $cart->remove();
            $commande->setState(1);
            $manager->flush();

            //Mailclient succes confirm
            $content = "Bonjour ".$commande->getUser()->getFirstname().",<br>
            Votre commande test est un succès, la confirmation de votre paiement test via stripe est confirmé. 
            Merci pour votre participation à la beta du site !<br>L'Atelier Berthie";
            $mail = new Mail();
            $mail->send($this->getUser()->getEmail(), 
            $commande->getUser()->getFirstname(), 
            "Votre commande de l'Atelier Berthie est validée !", 
            $content
            );
        }
        
        return $this->render('commande/succes.html.twig', [
            'commande' => $commande
        ]);
    }

    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="commande_erreur")
     */
    public function erreur(EntityManagerInterface $manager, CommandeRepository $commandeRepo, $stripeSessionId): Response
    {
        $commande = $commandeRepo->findOneByStripeSessionId($stripeSessionId);
        
        if(!$commande || $commande->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }
        
        if($commande->getState() == 0) {

            $commande->setState(5);
            $manager->flush();

            //Mail client echec confirm
            $content = "Bonjour".$commande->getUser()->getFirstname()."<br> Echec du paiement de votre commande test, votre commande n'a donc pas pu être validée!<br>
            Merci pour votre participation à la beta du site !<br>
            L'Atelier Berthie";
            $mail = new Mail();
            $mail->send($this->getUser()->getEmail(), 
            $commande->getUser()->getFirstname(), 
            "Votre commande de l'Atelier Berthie est validée !", 
            $content
            );
        }

        return $this->render('commande/erreur.html.twig', [
            'commande' => $commande
        ]);
    }
}
