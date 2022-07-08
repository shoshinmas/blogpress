<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $randomDate = (date_create_immutable(date('Y-m-d')));
        for ($i = 0; $i < 4; ++$i) {
            $blog = new Post();
            $blog->setTitle('Post title'. $i);
            $blog->setBody('Post body'. $i);
            $blog->setShortDesc('Post short description'. $i);
            $blog->setAuthor('Buda'. $i);
            $blog->setImage('NUP-177041-0001-0-62c6f3441d509.jpg');
            $blog->setCreatedAt($randomDate);
            $manager->persist($blog);

            $comment = new Comment();
            $comment->setPost($blog);
            $comment->setTitle('Comment title'.$i);
            $comment->setBody('Comment body'.$i);
            $comment->setAuthor('Buda'. $i);
            $comment->setCreatedAt($randomDate);
            $manager->persist($comment);
                        }

            $manager->flush();
        }
    }