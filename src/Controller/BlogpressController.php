<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogpressController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
    )
    {
    }

    #[Route('/', name: 'app_blogpress')]
    public function index(): Response
    {
        return $this->render('blogpress/index.html.twig', [
            'posts' => $this->postRepository->findAll(),
        ]);

    }
}
