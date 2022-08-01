<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Service\FileUploader;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
     #[Route('/product/new', name:'app_product_new', methods:['GET', 'POST'])]

    public function new(Request $request, FileUploader $fileUploader, ManagerRegistry $doctrine, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();


            // Cette condition est nécessaire car le champ 'brochure' n'est pas obligatoire
            // le fichier PDF doit donc être traité uniquement lorsqu'un fichier est téléchargé
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $product->setBrochureFilename($brochureFileName);
                $entityManager = $doctrine->getManager();
                $entityManager->persist($product);
                $entityManager->flush();
                return $this->redirectToRoute('app_main_home');
                }
            }

        return $this->renderForm('product/new.html.twig', [
            'form' => $form,
        ]);

    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_main_home', [], Response::HTTP_SEE_OTHER);
    }
}