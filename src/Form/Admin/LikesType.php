<?php

namespace App\Form\Admin;

use App\Entity\Article;
use App\Entity\Likes;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LikesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('article', EntityType::class, [
                'attr' => [
                    'class' => 'form-control form-group'
                ],
                'class' => Article::class,
                'choice_label' => 'title'
            ])
            ->add('author', EntityType::class, [
                'attr' => [
                    'class' => 'form-control form-group'
                ],
                'class' => User::class,
                'choice_label' => function ($author) {
                    return $author->getFirstName() . ' ' . $author->getLastName();
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Likes::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
