<?php

namespace App\Controller;

use App\Service\SendMailService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


#[Route('/', name: 'app_security')]
class SecurityController extends AbstractController
{

    // #[Route('/oubli-pass', name:'forgotten_password')]
    // public function forgottenPassword(
    //     Request $request,
    //     UserRepository $userRepository,
    //     TokenGeneratorInterface $tokenGenerator,
    //     EntityManagerInterface $entityManager,
    //     SendMailService $mail
    //     ): Response
    // {

    //     $form = $this->createForm(ResetPasswordRequestFormType::class);

    //     $form->handleRequest($request);

    //     if($form->isSubmitted() && $form->isValid()){
    //         // on va chercher un utilisateur par son Email
    //         $user = $userRepository->findOneByEmail($form->get('email')->getData());

    //         // on verifie si on a un utilisateur
    //         if($user){
    //             // On genere un token de reinitialisation
    //             $token = $tokenGenerator->generateToken();
    //             $user->setResetToken(1);
    //             $entityManager->persist($user);
    //             $entityManager->flush();

    //             // on genere un lien de reinitialisation de mot de passe
    //             $url = $this->generateUrl('reset_pass', ['token' => $token],
    //             UrlGeneratorInterface::ABSOLUTE_URL);

    //             // on crée les données du mail
    //             $contest = compact('url', 'user');

    //             // Envoi du mail
    //             $mail->send(
    //                 'no-reply@association-transhepate-bfc.org',
    //                 $user->getEmail(),
    //                 'Reinitialisation du mot de passe',
    //                 'password-reset',
    //                 $contest
    //             );

    //             $this->addFlash('success', 'Email envoyé avec succès');
    //             return $this->redirectToRoute('app_main_login');
    //         }
    //         // Si l'utilisateur n'existe pas
    //         $this->addFlash('danger', 'un problème est survenu cet utilisateur n\'existe pas');
    //         return $this->redirectToRoute('app_main_login');
    //     }

    //     return $this->renderForm('security/reset_password_request.html.twig', [
    //         'requestPassForm' => $form
    //     ]);
    // }

    // #[Route('/oubli-pass/{token}', name: 'reset_pass')]
    // public function resetPass(
    //     string $token,
    //     Request $request,
    //     UserRepository $userRepository,
    //     EntityManagerInterface $entityManager,
    //     UserPasswordHasherInterface $passwordHasher
    // ): Response
    // {
    //     // on verifie si on a ce token dans la base de données
    //     $user = $userRepository->findOneByResetToken($token);

    //     if($user){
    //         $form = $this->createForm(ResetPasswordFormType::class);

    //         $form->handleRequest($request);

    //         if($form->isSubmitted() && $form->isValid()){
    //             // on efface le token
    //             $user->setResetToken('');
    //             $user->setPassword(
    //                 $passwordHasher->hashPassword(
    //                     $user,
    //                     $form->get('password')->getData()
    //                 )
    //             );
    //             $entityManager->persist($user);
    //             $entityManager->flush();

    //             $this->addFlash('success', 'Mot de passe changé avec succès');
    //             return $this->redirectToRoute('app_main_login');
    //         }

    //         return $this->renderForm('security/reset_password.html.twig', [
    //             'passForm' => $form
    //         ]);
    //     }
    //     $this->addFlash('danger', 'jeton invalide');
    //     return $this->redirectToRoute('app_main_login');
    // }
}