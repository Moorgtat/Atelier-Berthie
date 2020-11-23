<?php

namespace App\Controller;


use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProduitRepository $produitRepo): Response
    {
        // $produits = $produitRepo->findByIsBest(true);

        $produits = $produitRepo->findAll();
        
        return $this->render('home/home.html.twig', [
            'produits' => $produits
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(ProduitRepository $produitRepo): Response
    {  
        return $this->render('home/about.html.twig');
    }
}
