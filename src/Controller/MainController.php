<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name:'app_home')]
    public function home(): Response
    {

        // Cette page appellera la vue template/main/index.html.twig
        return $this->render('main/home.html.twig', ['controller_name' => 'Accueil']);
    }


    #[Route('/contact', name:'app_contact')]
    public function contact(): Response
    {

        // Cette page appellera la vue template/main/contact.html.twig
        return $this->render('main/contact.html.twig');
    }
}