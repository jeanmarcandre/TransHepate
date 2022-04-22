<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}