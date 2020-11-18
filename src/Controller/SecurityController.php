<?php

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Entity\User;
use App\Service\Mail;
use App\Form\RegisterType;
use App\Form\ResetPasswordType;
use App\Repository\ResetPasswordRepository;
use App\Repository\UserRepository;
use DateTime;
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
        $user = new User ();
        $form = $this->createForm(RegisterType::class, $user);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $search_email = $userRepo->findOneByEmail($user->getEmail());

            if (!$search_email) {
                $password = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
                $user->setToken($this->generateToken());

                $manager->persist($user);
                $manager->flush();

                $url = $this->generateUrl('confirm_account', [
                    'token' => $user->getToken()
                ]);
                $content = "Bienvenue <strong>".$user->getFirstname()."</strong> à l'Atelier Berthie,<br>
                            Voici un lien pour confirmer ton inscription:<br>
                            <a href=".$url.">Activer mon compte</a><br>
                            L'Atelier Berthie";
                $mail = new Mail();
                $mail->send($user->getEmail(), 
                $user->getFirstname(), 
                "Bienvenue à l'Atelier Berthie !", 
                $content
                );
                $this->addFlash("success", "Votre inscription s'est effectué avec succès. Validé votre mail pour vous connecter à votre compte.");
                return $this->redirectToRoute("app_login");
            } else {
                $this->addFlash("error", "L'email que vous avez renseigné existe déjà.");
            }
        }
        
        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/confirmer-mon-compte/{token}", name="confirm_account")
     * @param string $token
     */
    public function confirmAccount(EntityManagerInterface $manager, UserRepository $userRepo, string $token)
    {
        $user = $userRepo->findOneBy(["token" => $token]);
        if($user) {
            $user->setToken(null);
            $user->setIsEnabled(true);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash("success", "Compte actif !");
            return $this->redirectToRoute("app_login");
        } else {
            $this->addFlash("error", "Ce compte n'existe pas !");
            return $this->redirectToRoute('home');
        }
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

    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */
    public function resetPassword(EntityManagerInterface $manager, UserRepository $userRepo, Request $request)
    {
        if($this->getUser())
        {
            return $this->redirectToRoute('home');
        }

        if($request->get('email'))
        {
            $user = $userRepo->findOneByEmail($request->get('email'));

            if($user)
            {
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new DateTime());

                $manager->persist($reset_password);
                $manager->flush();

                $url = $this->generateUrl('valid_reset_password', [
                    'token' => $reset_password->getToken()
                ]);

                $content = "<strong>".$user->getFirstname()."</strong>,<br>
                            Ta demande de réinitialisation a été prise en compte. <br>
                            Voici un lien pour réinitialiser ton mot de passe:<br>
                            <a href=".$url.">Valider la réinitialisation</a><br>
                            L'Atelier Berthie";

                $mail = new Mail();
                $mail->send($user->getEmail(), 
                $user->getFirstname(), 
                "Atelier Berthie - Réinitialisez votre mot de passe.", 
                $content
                );
                $this->addFlash("success", "Un mail de réinitialisation de mot de passe vous a été envoyé."); 
            }
            else 
            {
                $this->addFlash("error", "Aucun compte n'est associé à cet email."); 
            }
        }

        return $this->render('security/reset_password.html.twig');
    }

    /**
     * @Route("/mot-de-passe-oublie/{token}", name="valid_reset_password")
     */
    public function validReset(EntityManagerInterface $manager, Request $request, ResetPasswordRepository $resetRepo, UserPasswordEncoderInterface $encoder, $token)
    {
        $reset_password = $resetRepo->findOneByToken($token);

        if (!$reset_password)
        {
            return $this->redirectToRoute('reset_password');
        }

        $now = new DateTime();

        if ($now > $reset_password->getCreatedAt()->modify('+ 1 hour'))
        {
            $this->addFlash("error", "Votre demande de mot de passe a expiré. Merci de la renouveller."); 
           
            return $this->redirectToRoute('reset_password');        
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $new_password = $form->get('new_password')->getData();
            $password = $encoder->encodePassword($reset_password->getUser(), $new_password);

            $reset_password->getUser()->setPassword($password);
            $manager->flush();

            $this->addFlash("succes", "Votre mot de passe a bien été réinitialiser."); 
        
            return $this->redirectToRoute('app_login');        
        }

        return $this->render('security/update_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
