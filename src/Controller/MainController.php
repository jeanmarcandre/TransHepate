<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route(path: '/blog', name:'blog')]
    public function blog(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();
        dump($posts);
        // Cette page appellera la vue template/main/blog.html.twig
        // return $this->render('main/blog.html.twig');
    }

    #[Route(path: '/creer_un_article', name:'new_post')]
    public function newPost(Request $request, PostRepository $postRepository): Response
    {
        $post = new Post();

        $form = $this->createForm(PostFormType::class,$post);

        $form->handleRequest($request);

        /* gestion de la soumission du formulaire */
        if ($form->isSubmitted() && $form->isValid()) {

            $postRepository->add($post);

            $this->addFlash('success', 'Publication ajoutée');
            return $this->redirectToRoute('app_main_home');
        }

        return $this->renderForm('main/newPost.html.twig', [
            'form_post' => $form
        ]);
    }
}