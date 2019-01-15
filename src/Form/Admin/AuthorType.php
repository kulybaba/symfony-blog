<?php

namespace App\Form\Admin;

use App\Entity\User;
use App\Form\ProfileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-group'
                ]
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-group'
                ]
            ])
            ->add('profile', ProfileType::class, [
                'label_attr' => [
                    'class' => 'hidden'
                ]
            ])
            ->add('roles', CollectionType::class, [
                'entry_type' => ChoiceType::class,
                'entry_options' => [
                    'label' => false,
                    'choices' => [
                        'ROLE_READER' => 'ROLE_READER',
                        'ROLE_BLOGGER' => 'ROLE_BLOGGER',
                        'ROLE_ADMIN' => 'ROLE_ADMIN'
                    ],
                    'attr' => [
                        'class' => 'form-control form-group'
                    ]
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control form-group'
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'disabled' => true,
                'attr' => [
                    'class' => 'hidden'
                ],
                'label_attr' => [
                    'class' => 'hidden'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
