<?php

namespace App\Controller;

use \Mailjet\Resources;
use App\Form\NewsletterType;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/', name: 'app_emails')]
class MailerController extends AbstractController
{
    #[Route('/email', name: 'email')]

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

    /**** NEWSLETTER ****/
    // #[Route('/newsletter', name: 'newsletter')]

    // public function newsletter(Request $request): Response
    // {
    //     $mj = new \Mailjet\Client(getenv('MJ_APIKEY_PUBLIC'), getenv('MJ_APIKEY_PRIVATE'),true,['version' => 'v3.1']);
    //     $form = $this->createForm(NewsletterType::class);
    //     $form->handleRequest($request);

    //     /****  Première vérification à la soummission du formulaire pour vérifier si le Google Recaptcha est correctement validé  ****/
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $prenom = $form->get('prenom')->getData();
    //         $nom = $form->get('nom')->getData();
    //             $body = [
    //               'Messages' => [
    //                 [
    //                   'From' => [
    //                     'Email' => "contact@association-transhepate-bfc.org",
    //                     'Name' => "test"
    //                   ],
    //                   'To' => [
    //                     [
    //                       'Email' => "jean.marc.monin@gmail.com",
    //                       'Name' => "passenger 1"
    //                     ]
    //                   ],
    //                   'TemplateID' => 4123204,
    //                   'TemplateLanguage' => true,
    //                 //   'Subject' => "[[data:prénom:""]][[data:nom:""]]",
    //                   'Variables' => ['prenom'=> $prenom, 'nom'=> $nom]
    //                 ]
    //               ]
    //             ];
    //             $response = $mj->post(Resources::$Email, ['body' => $body]);
    //             // dd($response->getData());


    //     }
    //     return $this->renderForm('newsletter/newsletter.html.twig', [
    //         'form' => $form
    //     ]);
    // }

}