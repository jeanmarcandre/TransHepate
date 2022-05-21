<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ContactType;
use App\Entity\Permanences;
// Imports Login

// Import Post
use App\Form\PermanencesType;
// Imports Register
use Symfony\Component\Mime\Email;
use App\Controller\MainController;
use App\Form\RegistrationFormType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
// Import Google Recaptcha


// PAGINATOR
use Doctrine\ORM\EntityManagerInterface;
// EMAIL
use App\Repository\PermanencesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Knp\Component\Pager\PaginatorInterface;


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


    /****  FORMULAIRE DE CONTACT  ****/
    #[Route(path: '/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        /****  Première vérification à la soummission du formulaire pour vérifier si le Google Recaptcha est correctement validé  ****/
        if ($form->isSubmitted()) {


            // Récupération des données dans le formulaire sous-forme de tableau
            $contactFormData = $form->getData();

            // Test sur le champ phone (optionnel)
            // $phone = $contactFormData['phone'] ? $contactFormData['phone'] : 'non renseigné';

            // Création du message (Email Text)
            $email = new Email();
                    $email
                    ->from('jeanmarc.symfony@gmail.com')
                    ->to('jean.marc.monin21@gmail.com')
                    ->subject('vous avez reçu un email de Contact de ' . $contactFormData['fullname'])
                    ->text('Son nom : ' . $contactFormData['fullname']
                        . \PHP_EOL
                        . 'Son adresse email : ' . $contactFormData['email']
                        // . \PHP_EOL
                        // . 'Son téléphone : ' . $phone
                        . \PHP_EOL
                        . 'Son message : ' . $contactFormData['message'], 'text/plain');

            // Envoi de l'email
            $mailer->send($email);

            $this->addFlash('success', 'Votre message a bien été envoyé');

            return $this->redirectToRoute('app_main_contact');
            }

        return $this->renderForm('main/contact.html.twig', [
            'form' => $form]);
    }

    #[Route(path: '/transhepatebfc', name:'transhepatebfc')]
    public function transhepatebfc(PostRepository $postRepository, PermanencesRepository $permanencesRepository): Response
    {
        $tableaupermanences = $permanencesRepository->find(1)->getContent();

        // Cette page appellera la vue template/main/transhepatebfc.html.twig
        return $this->render('main/transhepatebfc.html.twig',[
            'tableau'=>$tableaupermanences
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

    #[Route(path: '/inscription', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response
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
