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
            ->to('jean.marc.monin21@gmail.com')
            ->cc('contact.transhepate.bfc@gmail.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Lorem ipsum')
            ->html('<p>Lorem ipsum...</p>')

            // attach a file stream
            ->text(fopen('/path/to/emails/user_signup.txt', 'r'))
            ->html(fopen('/path/to/emails/user_signup.html', 'r'));

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