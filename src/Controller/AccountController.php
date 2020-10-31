<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
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
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
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
}