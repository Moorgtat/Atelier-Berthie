<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Form\AdresseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdresseController extends AbstractController
{
    /**
     * @Route("/compte/adresse", name="adresse")
     */
    public function index(): Response
    {
        return $this->render('adresse/index.html.twig');
    }

    /**
     * @Route("/compte/ajouter-une-adresse", name="adresse_add")
     */
    public function add(): Response
    {
        $adresse = new Adresse();
        $form = $this->createForm(AdresseType::class, $adresse);

        return $this->render('adresse/adresse_add.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
