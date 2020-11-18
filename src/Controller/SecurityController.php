<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Mail;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="register")
     */
    public function register(UserRepository $userRepo, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
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

                $content = "Bonjour".$user->getFirstname()."<br>
                Votre inscription s'est effectué avec succès. 
                Vous pouvez dés à présent vous connecter et commander sur le site !";
                $mail = new Mail();
                $mail->send($user->getEmail(), 
                $user->getFirstname(), 
                "Bienvenue dans l'atelier de Berthie !", 
                $content
                );
                
                $notification = "Votre inscription s'est effectué avec succès. Vous pouvez dés à présent vous connecter à votre compte.";
            } else {
                $notification = "L'email que vous avez renseigné existe déjà.";
            }
        }
        
        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }

    /**
     * @Route("/connexion", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('account');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/déconnexion", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
