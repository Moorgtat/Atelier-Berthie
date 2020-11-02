<?php

namespace App\Controller;

use App\Classe\Search;
use App\Form\SearchType;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProduitController extends AbstractController
{
    /**
     * @Route("/nos-produits", name="produits")
     */
    public function index(ProduitRepository $repo, Request $request): Response
    {
        $produits = $repo->findAll();
        $search = new Search();

        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $produits = $repo->findWithSearch($search);
        }

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'form' => $form->createView()
        ]);
    }

        /**
     * @Route("/produit/{slug}", name="produit")
     */
    public function show($slug, ProduitRepository $repo): Response
    {
        $produit = $repo->findOneBySlug($slug);

        if (!$produit) {
            return $this->redirectToRoute('produits');
        }
        return $this->render('produit/show.html.twig', [
            'produit' => $produit
        ]);
    }
}
