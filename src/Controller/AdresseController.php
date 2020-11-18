<?php

namespace App\Controller;

use App\Service\Cart;
use App\Entity\Adresse;
use App\Form\AdresseType;
use App\Repository\AdresseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdresseController extends AbstractController
{
    /**
     * @Route("/compte/adresse", name="adresse")
     */
    public function all(): Response
    {
        return $this->render('adresse/all_adresses.html.twig');
    }

    /**
     * @Route("/compte/ajouter-une-adresse", name="adresse_add")
     */
    public function add(Cart $cart, Request $request, EntityManagerInterface $manager): Response
    {
        $adresse = new Adresse();
        $form = $this->createForm(AdresseType::class, $adresse);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $adresse->setUser($this->getUser());
            $manager->persist($adresse);
            $manager->flush();

            if($cart->get()) {
                return $this->redirectToRoute('commande_livraison');
            } else {
                return $this->redirectToRoute('adresse');
            }
        }

        return $this->render('adresse/add_adresse.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/modifer-une-adresse/{id}", name="adresse_edit")
     */
    public function edit($id, AdresseRepository $repo, Request $request, EntityManagerInterface $manager): Response
    {
        $adresse = $repo->findOneById($id);

        if(!$adresse || $adresse->getUser() != $this->getUser()) {
            return $this->redirectToRoute('adresse');
        }

        $form = $this->createForm(AdresseType::class, $adresse);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('adresse');
        }

        return $this->render('adresse/add_adresse.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/supprimer-une-adresse/{id}", name="adresse_delete")
     */
    public function delete($id, AdresseRepository $repo, EntityManagerInterface $manager): Response
    {
        $adresse = $repo->findOneById($id);

        if($adresse || $adresse->getUser() == $this->getUser()) {
            $manager->remove($adresse);
            $manager->flush();
        }

        return $this->redirectToRoute('adresse');
    }
}
