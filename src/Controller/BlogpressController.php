<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\Type\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogpressController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly CommentRepository $commentRepository
    )
    {
    }

    #[Route('/', name: 'app_blogpress', methods: ['GET','POST'])]
    public function index(): Response
    {
        return $this->render('blogpress/index.html.twig', [
            'posts' => $this->postRepository->findBy([], ['createdAt' => 'DESC']),
            'comments'=>$this->commentRepository->findAll(),
        ]);
    }
}
