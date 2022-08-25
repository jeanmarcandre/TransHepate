<?php

namespace App\Controller;

use Mailjet\Config;
use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\Form\FormError;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

#[Route('/', name: 'app_main_')]
class ContactController extends AbstractController
{

    /****  FORMULAIRE DE CONTACT  ****/
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]

    public function contact(
        Request $request,
        MailerInterface $mailer,
        ManagerRegistry $doctrine,
        // RecaptchaValidator $recaptchaValidator
        ): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        /****  Première vérification à la soummission du formulaire pour vérifier si le Google Recaptcha est correctement validé  ****/
        if ($form->isSubmitted() && $form->isValid()) {
            // dd('Formulaire valide c\'est super !');

            if(!empty($response) && !is_null($response))
            {
                $data = json_decode($response);
                if($data->success);
            }

                {
                // Récupération des données dans le formulaire sous-forme de tableau
                $contact->setContent($form->get('content')->getData());
                $contact->setName($form->get('name')->getData());
                $contact->setEmail($form->get('email')->getData());
                $entityManager = $doctrine->getManager();
                $entityManager->persist($contact);
                $entityManager->flush();

                // Création du message (Email Text)
                $email = (new Email());
                    $email
                    ->from($contact->getEmail())
                    ->to('contact.transhepate.bfc@gmail.com')
                    ->from('contact@association-transhepate-bfc.org')
                    // ->to('jean.marc.monin21@gmail.com')
                    ->subject('vous avez reçu un email de Contact de ' . $contact->getName())

                    ->text('Son IP : '.$_SERVER['REMOTE_ADDR']
                        . \PHP_EOL
                        .'Son nom : ' . $contact->getName()
                        . \PHP_EOL
                        . 'Son adresse email : ' . $contact->getEmail()
                        . \PHP_EOL
                        . 'Son message : ' . $contact->getContent(), 'text/plain')
                    ->html('Son IP : '.$_SERVER['REMOTE_ADDR'].'<br>'
                    . 'Son nom : ' . $contact->getName().'<br>'
                    . 'Son adresse email : ' . $contact->getEmail().'<br>'
                    . 'Son message : ' . $contact->getContent());

            // Envoi de l'email
            $mailer->send($email);

            $this->addFlash('success', 'Votre message a bien été envoyé');
            return $this->redirectToRoute('app_main_transhepatebfc', [], Response::HTTP_SEE_OTHER);
            }

        }
        return $this->renderForm('main/contact.html.twig', [
            'form' => $form,
        ]);
    }
}