<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Comment;
use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('author', TextType::class, ['attr' => ['class' => 'form-control']]);
        $builder->add('title', TextType::class, ['attr' => ['class' => 'form-control']]);
        $builder->add('body', TextareaType::class, ['attr' => ['class' => 'form-control']]);
        $builder->add('submit', SubmitType::class, [
            'attr' => ['class' => 'form-control',
                'label' => 'Submit'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Comment::class,
            ]
        );
    }
}