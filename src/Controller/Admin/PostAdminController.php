<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\Type\BlogFormType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostAdminController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
    )
    {
    }

    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        return $this->render('blogpress/admin/index.html.twig', [
            'posts' => $this->postRepository->findAll(),
        ]);

    }

    #[Route('/admin/create', name: 'app_admin_post_create')]
    public function createBlog(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(BlogFormType::class, new Post());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Image cannot be saved.');
                }
                $post->setImage($newFilename);
            }
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'Post was created!');
            return $this->redirectToRoute('app_admin_dashboard');
        }
        return $this->render('blogpress/admin/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
