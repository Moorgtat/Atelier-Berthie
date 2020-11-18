<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/compte", name="account")
     */
    public function compte(): Response
    {
        return $this->render('account/compte.html.twig');
    }

    /**
     * @Route("/compte/modifier-mdp", name="account_password")
     */
    public function password(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $old_password = $form->get('old_password')->getData();

            if ($encoder->isPasswordValid($user, $old_password)) {

                $new_password = $form->get('new_password')->getData();
                $password = $encoder->encodePassword($user, $new_password);

                $user->setPassword($password);
                $manager->flush();

                $notification = "Votre mot de passe a bien été mis à jour.";
            
            } else {

                $notification = "Votre mot de passe actuel n'est pas le bon.";

            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }

    /**
     * @Route("/compte/mes-commandes", name="account_commande")
     */
    public function allcommandes(CommandeRepository $commandeRepo): Response
    {
        $commandes = $commandeRepo->findSuccesCommandes($this->getUser());
        return $this->render('account/commandes.html.twig', [
            'commandes' => $commandes
        ]);
    }

    /**
     * @Route("/compte/mes-commandes/{reference}", name="account_commande_show")
     */
    public function showcommande(CommandeRepository $commandeRepo, $reference): Response
    {
        $commande = $commandeRepo->findOneByReference($reference);
        
        if(!$commande || $commande->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_commande');
        }
        
        return $this->render('account/commande_show.html.twig', [
            'commande' => $commande
        ]);
    }
}
