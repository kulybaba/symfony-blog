<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-group'
                ],
            ])
            ->add('shortText', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-group',
                    'rows' => 5
                ]
            ])
            ->add('text', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control form-group',
                    'rows' => 10
                ]
            ])
            ->add('category', EntityType::class, [
                'attr' => [
                    'class' => 'form-control form-group'
                ],
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('tag', EntityType::class, [
                'label' => 'Tags',
                'class' => Tag::class,
                'choice_label' => 'text',
                'multiple' => true,
                'expanded' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
