<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Service\FileUploader;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProductController extends AbstractController
{
    /**
     * @Route("/product/new", name="app_product_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SluggerInterface $slugger, FileUploader $fileUploader, ManagerRegistry $doctrine, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();
                // dd($product);
                // $entityManager = $doctrine->getManager();
                // $entityManager->persist($product);
                // $entityManager->flush();

            // Cette condition est nécessaire car le champ 'brochure' n'est pas obligatoire
            // le fichier PDF doit donc être traité uniquement lorsqu'un fichier est téléchargé
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $product->setBrochureFilename($brochureFileName);
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // cela est nécessaire pour inclure en toute sécurité le nom du fichier dans l'URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Déplacer le fichier dans le répertoire où sont stockées les brochures
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );

                } catch (FileException $e) {
                    // ... Gére l'exception si quelque chose se produit pendant le téléchargement du fichier
                }

                // Met à jour la propriété 'brochureFilename' pour stocker le nom du fichier PDF
                // à la place de son contenu
                $product->setBrochureFilename($newFilename);
            }

            // ... Persiste la variable $brochure ou tout autre travail

            return $this->redirectToRoute('app_main_home');
        }

        return $this->renderForm('product/new.html.twig', [
            'form' => $form,
        ]);
    }
}