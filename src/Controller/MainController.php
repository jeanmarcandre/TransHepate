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
use Doctrine\ORM\EntityManager;
// Import Google Recaptcha
use App\Service\SendMailService;
// use App\Recaptcha\RecaptchaValidator;

// PAGINATOR
use App\Form\RegistrationFormType;
// EMAIL
use App\Repository\PostRepository;
// use Symfony\Component\Form\FormError;
use App\Repository\UserRepository;
use App\Form\ResetPasswordFormType;
use App\Repository\ActionsRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PermanencesRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\ResetPasswordRequestFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\Loader\Configurator\form;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

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
            $this->addFlash('warning', 'Vous êtes déjà connecté');
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
    public function forgottenPassword(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        SendMailService $mail
        ): Response
    {

        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // on va chercher un utilisateur par son Email
            $user = $userRepository->findOneByEmail($form->get('email')->getData());

            // on verifie si on a un utilisateur
            if($user){
                // On genere un token de reinitialisation
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                // on genere un lien de reinitialisation de mot de passe
                $url = $this->generateUrl('app_main_reset_pass', ['token' => $token],
                UrlGeneratorInterface::ABSOLUTE_URL);

                // on crée les données du mail
                $contest = compact('url', 'user');

                // Envoi du mail
                $mail->send(
                    'no-reply@association-transhepate-bfc.org',
                    $user->getEmail(),
                    'Reinitialisation du mot de passe',
                    'password_reset',
                    $contest
                );

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_main_login');
            }
            // Si l'utilisateur n'existe pas
            $this->addFlash('danger', 'un problème est survenu cet utilisateur n\'existe pas');
            return $this->redirectToRoute('app_main_login');
        }

        return $this->renderForm('security/reset_password_request.html.twig', [
            'requestPassForm' => $form
        ]);
    }

    #[Route('/oubli-pass/{token}', name: 'reset_pass')]
    public function resetPass(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        // on verifie si on a ce token dans la base de données
        $user = $userRepository->findOneByResetToken($token);

        if($user){
            $form = $this->createForm(ResetPasswordFormType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                // on efface le token
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe changé avec succès');
                return $this->redirectToRoute('app_main_login');
            }

            return $this->renderForm('security/reset_password.html.twig', [
                'passForm' => $form
            ]);
        }
        $this->addFlash('danger', 'jeton invalide');
        return $this->redirectToRoute('app_main_login');
    }

    /****  INSCRIPTION  ****/
    #[Route('/inscription', name: 'register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator
    ): Response {
        if ($this->getUser()) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit');
            return $this->redirectToRoute('app_main_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            // $recaptchaResponse = $request->request->get('g-recaptcha-response', null);
            // dd ($recaptchaResponse);
            // if ($recaptchaResponse == null || !$recaptcha->verify( $recaptchaResponse, $request->server->get('REMOTE_ADDR') )){

            //     $form->addError(new FormError('Le Captcha doit être validé !'));
            // }

            if ($form->isValid()) {

                // encode the plain password
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
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
    #[Route('/recherche', name: 'search', methods: ['GET'])]
    public function search(Request $request, PostRepository $postRepository, PaginatorInterface $paginator): Response
    {
        // Récupération du numéro de la page demangée
        $requestedPage = $request->query->getInt('page', 1);

        // Vérification que le numéro est positif
        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

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
