<?php

namespace App\Controller;

use App\Repository\HeaderRepository;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProduitRepository $produitRepo, HeaderRepository $headerRepo): Response
    {
        $produits = $produitRepo->findByIsBest(true);
        $headers = $headerRepo->findAll();
        
        return $this->render('home/accueil.html.twig', [
            'produits' => $produits,
            'headers' => $headers
        ]);
    }
}
