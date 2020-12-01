<?php

namespace App\Controller;


use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Length;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProduitRepository $produitRepo): Response
    {
        // $produits = $produitRepo->findByIsBest(true);

        $produits = $produitRepo->findAll();

        $moitie = sizeof($produits) /2;
        $l_produits = array_slice($produits, $moitie);

        rsort($produits);
        $f_produits = array_slice($produits, $moitie);
        
        return $this->render('home/home.html.twig', [
            'f_produits' => $f_produits,
            'l_produits' => $l_produits
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
