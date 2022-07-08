<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Entity\Comment;
use App\Form\Type\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BlogpressController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly CommentRepository $commentRepository
    )
    {
    }

    #[Route('/', name: 'app_blogpress')]
    public function index(): Response
    {
        return $this->render('blogpress/index.html.twig', [
            'posts' => $this->postRepository->findBy([], ['createdAt' => 'DESC']),
            'comments'=>$this->commentRepository->findAll(),
        ]);
    }

    #[Route(path: '/post/{id}', name: 'app_user_post_comment')]
    public function placeComment(Post $post, EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(CommentFormType::class, new Comment());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setCreatedAt(date_create_immutable());
            $post->addComment($comment);
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('app_blogpress');
        }
        return $this->render('blogpress/showPost.html.twig', [
            'comment_form' => $form->createView()
        ]);
}
}
