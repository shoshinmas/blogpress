<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('author', TextType::class, ['attr' => ['class' => 'form-control']]);
        $builder->add('title', TextType::class, ['attr' => ['class' => 'form-control']]);
        $builder->add('shortDesc', TextType::class, ['attr' => ['class' => 'form-control']]);
        $builder->add('body', TextType::class, ['attr' => ['class' => 'form-control']]);
        $builder->add('createdAt', \DateTimeImmutable::class, ['attr' => ['class' => 'form-control', 'visible' => 'hidden']]);
        $builder->add('imageFile', FileType::class, [
            'attr'     => ['class' => 'form-control',],
            'mapped' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Post::class,
            ]
        );
    }
}