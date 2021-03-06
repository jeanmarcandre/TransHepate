<?php

namespace App\Controller;

use App\Entity\User;

// Imports Login

// Import Post
use App\Entity\Actions;
use App\Form\ActionsType;
// Imports Register
use App\Entity\Permanences;
use App\Form\PermanencesType;
use App\Form\RegistrationFormType;
// Import Google Recaptcha
use App\Repository\PostRepository;
// use App\Recaptcha\RecaptchaValidator;

// PAGINATOR
use App\Repository\UserRepository;
// EMAIL
use App\Repository\ActionsRepository;
// use Symfony\Component\Form\FormError;
use App\Repository\ProductRepository;
use App\Repository\PermanencesRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\ResetPasswordRequestFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\Loader\Configurator\form;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/', name: 'app_main_')]
class MainController extends AbstractController
{

    /***   ACCUEIL  ***/
    #[Route('/', name: 'home')]
    public function home(PostRepository $postRepository, ProductRepository $productRepository, ActionsRepository $actionsRepository): Response
    {
            // $affichageactions = $actionsRepository->find()->getContent();
            // dd ($affichageactions);
            // Cette page appellera la vue template/main/nos_actions.html.twig
            $product = $productRepository->findAll();
            $lastProduct = array_slice($product,count($product)-2);


        return $this->render('main/home.html.twig', [
            'posts' => $postRepository->findBy([], ['createdAt' => 'desc']),
            // 'affichage' => $affichageactions,
            'products' => $lastProduct
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
    #[Route('/transhepatebfc', name: 'transhepatebfc')]
    public function transhepatebfc(PermanencesRepository $permanencesRepository): Response
    {
        $tableaupermanences = $permanencesRepository->find(1)->getContent();

        // Cette page appellera la vue template/main/transhepatebfc.html.twig
        return $this->render('main/transhepatebfc.html.twig', [
            'tableau' => $tableaupermanences
        ]);
    }




    /**** MENTIONS LEGALES ****/
    #[Route('/mentions_legales', name: 'mentions_legales')]
    public function mentions_legales(): Response
    {

        // Cette page appellera la vue template/main/mentions_legales.html.twig
        return $this->render('main/mentions_legales.html.twig', []);
    }

    /**** HELLOASSO ****/
    #[Route('/helloasso', name: 'helloasso')]
    public function helloasso(): Response
    {

        // Cette page appellera la vue template/main/helloasso.html.twig
        return $this->render('main/helloasso.html.twig', []);
    }

    /**** ADHESIONS ****/
    #[Route('/adhesion', name: 'adhesion')]
    public function adhesion(): Response
    {

        // Cette page appellera la vue template/main/adhesion.html.twig
        return $this->render('main/adhesion.html.twig', []);
    }

    /**** BENEVOLAT ****/
    #[Route('/benevole', name: 'benevole')]
    public function benevole(): Response
    {

        // Cette page appellera la vue template/main/benevole.html.twig
        return $this->render('main/benevole.html.twig', []);
    }

    /****  CONNEXION  ****/
    #[Route('/connexion', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->addFlash('warning', 'Vous ??tes d??j?? connect??');
            return $this->redirectToRoute('app_main_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('main/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/deconnexion', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/oubli-pass', name:'forgotten_password')]
    public function forgottenPassword(): Response
    {

        $form = $this->createForm(ResetPasswordRequestFormType::class);

        return $this->renderForm('security/reset_password_request.html.twig', [
            'requestPassForm' => $form
        ]);
    }

    /****  INSCRIPTION  ****/
    #[Route('/inscription', name: 'register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository
    ): Response {
        if ($this->getUser()) {
            $this->addFlash('warning', 'Vous ??tes d??j?? inscrit');
            return $this->redirectToRoute('app_main_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            // $recaptchaResponse = $request->request->get('g-recaptcha-response', null);
            // dd ($recaptchaResponse);
            // if ($recaptchaResponse == null || !$recaptcha->verify( $recaptchaResponse, $request->server->get('REMOTE_ADDR') )){

            //     $form->addError(new FormError('Le Captcha doit ??tre valid?? !'));
            // }

            if ($form->isValid()) {

                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $userRepository->add($user);
                $this->addFlash('success', 'Vous ??tes bien inscrit');

                // do anything else you need here, like send an email

                return $this->redirectToRoute('app_main_login');
            }
        }

        return $this->renderForm('main/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /****  RECHERCHE  ****/
    #[Route('/recherche', name: 'search', methods: ['GET'])]
    public function search(Request $request, PostRepository $postRepository, PaginatorInterface $paginator): Response
    {
        // R??cup??ration du num??ro de la page demang??e
        $requestedPage = $request->query->getInt('page', 1);

        // V??rification que le num??ro est positif
        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        // On r??cup??re le contenu du champ de recherche
        $search = $request->query->get('search', '');

        // Utilisation de la m??thode pr??sente dans le repository pour rechercher l'??l??ment (dans le titre et le contenu)
        $posts = $postRepository->findBySearch($search);

        // R??cup??ration des publications pagin??es
        $posts_paginate = $paginator->paginate(
            $posts, // Requ??te de r??cup??ration des publications
            $requestedPage, // Num??ro de la page demand??e dans $request
            $this->getParameter('app_search.post_number') // Nombre de publications par page (dans les param??tres)
        );

        // R??ponse -> envoyer une page contenant les ??l??ments ?? afficher
        return $this->render('blog/search.post.html.twig', [
            'posts' => $posts_paginate,
        ]);
    }

    /**** TABLEAU DES PERMANENCES (ADMIN) ****/
    #[Route('/new-permanences', name: 'permanences', methods: ['GET', 'POST'])]
    public function new(Request $request, PermanencesRepository $permanencesRepository, ManagerRegistry $doctrine): Response
    {
        if ($permanencesRepository->find(1)) {
            $permanences = $permanencesRepository->find(1);
        } else {
            $permanences = new Permanences();
        }

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

    /**** TABLEAU DES ACTIONS (ADMIN) ****/
    #[Route('/actions', name: 'actions', methods: ['GET', 'POST'])]
    public function Actions(Request $request, ActionsRepository $actionsRepository, ManagerRegistry $doctrine): Response
    {
        if ($actionsRepository->find(1)) {
            $actions = $actionsRepository->find(1);
        } else {
            $actions = new Actions();
        }

        $form = $this->createForm(ActionsType::class, $actions);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $actions->setContent($form->get('content')->getData());
            // dd($actions);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($actions);
            $entityManager->flush();
            // $actionsRepository->add($actions);
            // dd($actions);
            return $this->redirectToRoute('app_main_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('main/actions.html.twig', [
            'form' => $form,
        ]);
    }
}
