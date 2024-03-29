<?php

namespace App\Controller;

use App\Service\Cart;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/mon-panier", name="cart")
     */
    public function index(Cart $cart): Response
    {
        return $this->render('cart/panier.html.twig', [
            'cart' => $cart->getFull()
        ]);
    }

    /**
     * @Route("/mon-panier/ajouter/{id}", name="add_to_cart")
     */
    public function add(Cart $cart, $id): Response
    {
        $cart->add($id);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/mon-panier/supprimer", name="remove_my_cart")
     */
    public function remove(Cart $cart): Response
    {
        $cart->remove();
        return $this->redirectToRoute('produits');
    }

    /**
     * @Route("/mon-panier/supprimer/{id}", name="delete_to_cart")
     */
    public function delete(Cart $cart, $id): Response
    {
        $cart->delete($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/mon-panier/diminuer/{id}", name="decrease_to_cart")
     */
    public function decrease(Cart $cart, $id): Response
    {
        $cart->decrease($id);

        return $this->redirectToRoute('cart');
    }
}
