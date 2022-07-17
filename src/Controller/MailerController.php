<?php

namespace App\Controller;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/', name: 'app_emails')]
class MailerController extends AbstractController
{
    /**
     * @Route("/email")
     */
    public function sendEmail(MailerInterface $mailer): Void
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Lorem ipsum')
            ->html('<p>Lorem ipsum...</p>');

        $mailer->send($email);

        // ...
    }

     /**** INSCRIPTION ****/
     #[Route(path: '/register', name: 'register')]
     public function register(): Response
     {
 
         // Cette page appellera la vue template/main/adhesion.html.twig
         return $this->render('emails/register.html.twig', []);
     }
}