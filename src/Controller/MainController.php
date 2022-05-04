<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\twig;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
            return $this->redirectToRoute('app_main_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/deconnexion', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
