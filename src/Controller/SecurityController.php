<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


#[Route('/', name: 'app_security')]
class SecurityController extends AbstractController
{

    // #[Route('/oubli-pass', name:'forgotten_password')]
    // public function forgottenPassword(): Response
    // {

    //     $form = $this->createForm(ResetPasswordRequestFormType::class);

    //     return $this->render('security/reset_password_request.html.twig', [
    //         'requestPassForm' => $form
    //     ]);
    // }

    /****  CONNEXION  ****/
    // #[Route('/connexion', name: 'login')]
    // public function login(AuthenticationUtils $authenticationUtils): Response
    // {
    //     if ($this->getUser()) {
    //         $this->addFlash('warning', 'Vous êtes déjà connecté');
    //         return $this->redirectToRoute('app_main_home');
    //     }

    //     // get the login error if there is one
    //     $error = $authenticationUtils->getLastAuthenticationError();
    //     // last username entered by the user
    //     $lastUsername = $authenticationUtils->getLastUsername();

    //     return $this->render('main/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    // }

    // #[Route('/deconnexion', name: 'logout')]
    // public function logout(): void
    // {
    //     throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    // }
}