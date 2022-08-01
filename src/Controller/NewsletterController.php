<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Repository\NewsletterRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/', name: 'app_newsletter_')]
class NewsletterController extends AbstractController
{
    #[Route('/newsletter', name: 'newsletter', methods: ['GET', 'POST'])]
    public function newsletter(Request $request, ManagerRegistry $doctrine, NewsletterRepository $newsletterRepository): Response
    {
        if ($newsletterRepository->find(1)) {
            $newsletter = $newsletterRepository->find(1);
        } else {
        $newsletter = new Newsletter();
        }

        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        $newsletter->setEmail($form->get('email')->getData());
        $entityManager = $doctrine->getManager();
        $entityManager->persist($newsletter);
        $entityManager->flush();

        $form = $this->createFormBuilder($newsletter)
            ->add('newsletter', TextType::class)
            ->add('dueDate', DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Newsletter'])
            ->getForm();

        $newsletterRepository->add($newsletter);
        $this->addFlash('success', 'la Newsletter à bien été envoyée');
        }

        return $this->renderForm('newsletter/newsletter.html.twig', [
            'form' => $form,
        ]);
    }

    /**** INSCRIPTION A LA NEWSLETTER ****/
    #[Route(path: 'newsletter/register', name: 'register')]
    public function register(): Response
    {

        // Cette page appellera la vue template/main/adhesion.html.twig
        return $this->render('newsletter/register.html.twig', []);
    }
}