<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/inscription", name="register")
     */
    public function index(UserRepository $userRepo, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;

        $user = new User ();
        $form = $this->createForm(RegisterType::class, $user);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $search_email = $userRepo->findOneByEmail($user->getEmail());

            if (!$search_email) {
                $password = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
                $manager->persist($user);
                $manager->flush();

                $content = "Votre inscription s'est effectué avec succès. Vous pouvez dés à présent vous connecter et commander sur le site !";
                $mail = new Mail();
                $mail->send($user->getEmail(), 
                $user->getFirstname(), 
                "Bienvenue dans l'atelier de Berthie !", 
                $content
                );
                
                $notification = "Votre inscription s'est effectué avec succès. Vous pouvez dés à présent vous connecter à votre compte.";
            } else {
                $notification = "L'email que vous avez rensigné existe déjà.";
            }
        }
        
        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
