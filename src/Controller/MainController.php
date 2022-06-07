<?php

namespace App\Controller;

use App\Entity\User;

use App\Entity\Permanences;
// Imports Login

// Import Post
use App\Form\PermanencesType;
// Imports Register
use App\Form\RegistrationFormType;
use App\Repository\PostRepository;
// Import Google Recaptcha
use App\Repository\UserRepository;
use App\Recaptcha\RecaptchaValidator;

// PAGINATOR
use Symfony\Component\Form\FormError;
// EMAIL

use App\Repository\PermanencesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/', name: 'app_main_')]
class MainController extends AbstractController
{

    /***   ACCUEIL  ***/
    #[Route(path:'/', name:'home')]
    public function home(PostRepository $postRepository): Response
    {
        return $this->render('main/home.html.twig', [
            'posts' => $postRepository->findBy([], ['createdAt' => 'desc']),
        ]);
    }

    #[Route('/utilisateurs', name: 'user_show', methods: ['GET'])]
    public function show(): Response
    {
        $user = $this->getUser();
        return $this->render('admin/users/user.show.html.twig', [
            'user' => $user,
        ]);
    }

    /**** PAGE CONTACT ****/
    #[Route(path: '/transhepatebfc', name:'transhepatebfc')]
    public function transhepatebfc(PermanencesRepository $permanencesRepository): Response
    {
        $tableaupermanences = $permanencesRepository->find(1)->getContent();

        // Cette page appellera la vue template/main/transhepatebfc.html.twig
        return $this->render('main/transhepatebfc.html.twig',[
            'tableau'=>$tableaupermanences
        ]);
    }

    /**** MENTIONS LEGALES ****/
    #[Route(path: '/mentions_legales', name:'mentions_legales')]
    public function mentions_legales(): Response
    {

        // Cette page appellera la vue template/main/mentions_legales.html.twig
        return $this->render('main/mentions_legales.html.twig',[

        ]);
    }

    /****  CONNEXION  ****/
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

    /****  INSCRIPTION  ****/
    #[Route(path: '/inscription', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                             UserRepository $userRepository, RecaptchaValidator $recaptcha): Response
    {
        if ($this->getUser()) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit');
            return $this->redirectToRoute('app_main_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $recaptchaResponse = $request->request->get('g-recaptcha-response', null);
            // dd ($recaptchaResponse);
            // if ($recaptchaResponse == null || !$recaptcha->verify( $recaptchaResponse, $request->server->get('REMOTE_ADDR') )){

            //     $form->addError(new FormError('Le Captcha doit être validé !'));
            // }

            if( $form->isValid()) {

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
        }

        return $this->renderForm('main/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /****  RECHERCHE  ****/
    #[Route(path: '/recherche', name: 'search', methods: ['GET'])]
    public function search(Request $request, PostRepository $postRepository, PaginatorInterface $paginator): Response
    {
        // Récupération du numéro de la page demangée
        $requestedPage = $request->query->getInt('page', 1);

        // Vérification que le numéro est positif
        if ($requestedPage < 1) { throw new NotFoundHttpException(); }

        // On récupère le contenu du champ de recherche
        $search = $request->query->get('search', '');

        // Utilisation de la méthode présente dans le repository pour rechercher l'élément (dans le titre et le contenu)
        $posts = $postRepository->findBySearch($search);

        // Récupération des publications paginées
        $posts_paginate = $paginator->paginate(
            $posts, // Requête de récupération des publications
            $requestedPage, // Numéro de la page demandée dans $request
            $this->getParameter('app_search.post_number') // Nombre de publications par page (dans les paramètres)
        );

        // Réponse -> envoyer une page contenant les éléments à afficher
        return $this->render('blog/search.post.html.twig', [
            'posts' => $posts_paginate,
        ]);
    }

    /**** TABLEAU DES PERMANENCES (ADMIN) ****/
    #[Route('/new-permanences', name: 'permanences', methods: ['GET', 'POST'])]
    public function new(Request $request, PermanencesRepository $permanencesRepository, ManagerRegistry $doctrine): Response
    {
        if($permanencesRepository->find(1)){
            $permanences = $permanencesRepository->find(1);

        }
        else{$permanences = new Permanences();}

        $form = $this->createForm(PermanencesType::class, $permanences);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $permanences->setContent($form->get('content')->getData());
            // dd($permanences);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($permanences);
            $entityManager->flush();
            // $permanencesRepository->add($permanences);
            // dd($permanences);
            return $this->redirectToRoute('app_main_transhepatebfc', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('main/permanences.html.twig', [
            'form' => $form,
        ]);
    }
}
