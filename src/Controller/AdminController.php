<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Comment;
use App\Entity\Permanences;
use App\Form\PermanencesType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use App\Repository\PermanencesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
// CONTROLE DES ROLES
use Symfony\Component\HttpFoundation\Response;
// PAGINATOR
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
#[Route('/administration', name: 'app_admin_')]
class AdminController extends AbstractController
{
    // Injection des différents Repository directement par le Construct
    private UserRepository $userRepo;
    private PostRepository $postRepo;
    private CommentRepository $commentRepo;
    private PermanencesRepository $permanencesRepo;

    public function __construct(UserRepository $userRepository,
                                PostRepository $postRepository,
                                CommentRepository $commentRepository,
                                PermanencesRepository $permanencesRepository)
    {
        $this->userRepo = $userRepository;
        $this->postRepo = $postRepository;
        $this->commentRepo = $commentRepository;
        $this->permanencesRepo = $permanencesRepository;
    }

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('admin/home.admin.html.twig', [
            'Nb_Users' => $this->userRepo->countNumberUsers(),
            'Nb_Posts' => $this->postRepo->countNumberPosts(),
            'Nb_Comments' => $this->commentRepo->countNumberComments(),
        ]);
    }

    /**********  USERS  ***********/
    #[Route('/utilisateurs', name: 'user_index')]
    public function userIndex(Request $request, PaginatorInterface $paginator): Response
    {

        $requestedPage = $request->query->getInt('page', 1);
        if ($requestedPage < 1) { throw new NotFoundHttpException(); }

        $users_query = $this->userRepo->findAllOrderedQuery($this->getParameter('app_admin.user_number'));

        $users_paginate = $paginator->paginate( $users_query, $requestedPage, $this->getParameter('app_admin.user_number') );        

        return $this->render('admin/users/user.index.html.twig', [
            'users' => $users_paginate,
        ]);
    }


    #[Route('/utilisateurs/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/users/user.show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/utilisateurs/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepo->add($user);
            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/utilisateurs/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->userRepo->remove($user);
        }

        return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/permanences', name: 'permanences', methods: ['GET', 'POST'])]
    public function permanences(Request $request): Response
    {
        $permanences = new Permanences();
        $form = $this->createForm(PermanencesType::class, $permanences);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->permanencesRepo->add($permanences);

            return $this->redirectToRoute('app_main_transhepatebfc', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('permanence/permanences.html.twig', [
            'form' => $form,
        ]);
    }
}