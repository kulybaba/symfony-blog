<?php

namespace App\Form\Admin;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-group'
                ]
            ])
            ->add('author', EntityType::class, [
                'attr' => [
                    'class' => 'form-control form-group'
                ],
                'class' => User::class,
                'choice_label' => function ($author) {
                    return $author->getFirstName() . ' ' . $author->getLastName();
                }
            ])
            ->add('article', EntityType::class, [
                'attr' => [
                    'class' => 'form-control form-group'
                ],
                'class' => Article::class,
                'choice_label' => 'title'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
