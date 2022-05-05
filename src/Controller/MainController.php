<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
// Imports Login
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
// Imports Register
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationFormType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/', name: 'app_main_')]
class MainController extends AbstractController
{
    #[Route(path:'/', name:'home')]
    public function home(): Response
    {
        // Cette page appellera la vue template/main/index.html.twig
        return $this->render('main/home.html.twig');
    }


    #[Route(path: '/contact', name:'contact')]
    public function contact(): Response
    {
        // Cette page appellera la vue template/main/contact.html.twig
        return $this->render('main/contact.html.twig');
    }

    #[Route(path: '/transhepatebfc', name:'transhepatebfc')]
    public function transhepatebfc(PostRepository $postRepository): Response
    {
        // Cette page appellera la vue template/main/transhepatebfc.html.twig
        return $this->render('main/transhepatebfc.html.twig');
    }

    #[Route(path: '/connexion', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->addFlash('warning', 'Vous êtes déjà connecté');
            return $this->redirectToRoute('app_main_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('main/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/deconnexion', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/inscription', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $useRepository): Response
    {
        if ($this->getUser()) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit');
            return $this->redirectToRoute('app_main_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
            );


            $userRepository->add($user);
            $this->addFlash('success', 'Vous êtes bien inscrit');

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_main_login');
        }

        return $this->renderForm('main/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
