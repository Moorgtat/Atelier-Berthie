<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $manager, CommandeRepository $commandeRepo, ProduitRepository $produitRepo, Cart $cart, $reference): Response
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
                'unit_amount' => $commande->getTransporteurPrix() * 100,
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
}
