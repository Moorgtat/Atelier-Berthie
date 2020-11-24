<?php

namespace App\Controller;

use App\Service\Search;
use App\Form\SearchType;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProduitController extends AbstractController
{
    /**
     * @Route("/shop", name="produits")
     */
    public function allProduit(ProduitRepository $repo): Response
    {
        // $search = new Search();

        // $form = $this->createForm(SearchType::class, $search);
        // $form->handleRequest($request);

        // if($form->isSubmitted() && $form->isValid()) {
        //     $produits = $repo->findWithSearch($search);
        // } else {
        //     $produits = $repo->findAll();
        // }

        $couleur = ["#EB9583", "#584E71"];

        $produits = $repo->findAll();

        return $this->render('produit/shop.html.twig', [
            'produits' => $produits,
            'couleur' => $couleur
            // 'form' => $form->createView()
        ]);
    }

        /**
     * @Route("/produit/{slug}", name="produit")
     */
    public function show($slug, ProduitRepository $repo): Response
    {
        $produit = $repo->findOneBySlug($slug);
        // $produits = $repo->findByIsBest(true);
        if (!$produit) {
            return $this->redirectToRoute('produits');
        }
        
        return $this->render('produit/show.html.twig', [
            'produit' => $produit
            // 'produits' => $produits
        ]);
    }
}
