<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\Type\BlogFormType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    public function createPost(Post $post, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $post->setImage(new File(sprintf('%s/%s', $this->getParameter('image_directory'), $post->getImage())));
        $form = $this->createForm(BlogFormType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post      = $form->getData();
            $imageFile = $form->get('imageFile')->getData();
            $this->imageHandler($imageFile, $slugger, $post, $entityManager);
            $this->addFlash('success', 'Post was edited!');
        }

        return $this->render('blogpress/admin/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SluggerInterface $slugger
     * @return Response
     */
    #[Route('/admin/edit/{id}', name: 'app_admin_post_edit')]
    public function editPost(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(BlogFormType::class, new Post());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $imageFile = $form->get('imageFile')->getData();
            $post->setCreatedAt(date_create_immutable());
            $this->imageHandler($imageFile, $slugger, $post, $entityManager);
            $this->addFlash('success', 'Post was created!');
            return $this->redirectToRoute('app_admin_dashboard');
        }
        return $this->render('blogpress/admin/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/admin/delete/{id}', name: 'app_admin_post_delete')]
    public function deletePost(Post $post, EntityManagerInterface $em): RedirectResponse
    {
        $em->remove($post);
        $em->flush();
        $this->addFlash('success', 'Post was removed!');

        return $this->redirectToRoute('app_admin_dashboard');
    }

    /**
     * @param mixed $imageFile
     * @param SluggerInterface $slugger
     * @param Post $post
     * @param EntityManagerInterface $entityManager
     */
    private function imageHandler(mixed $imageFile, SluggerInterface $slugger, Post $post, EntityManagerInterface $entityManager): void
    {
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

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
    }
}
