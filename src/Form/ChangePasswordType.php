<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Password',
                    'attr' => [
                        'class' => 'form-control form-group',
                    ]
                ],
                'second_options' => [
                    'label' => 'Repeat password',
                    'attr' => [
                        'class' => 'form-control form-group',
                    ]
                ]
            ])->add('firstName', TextType::class, [
                'disabled' => true,
                'attr' => [
                    'class' => 'hidden'
                ],
                'label_attr' => [
                    'class' => 'hidden'
                ]
            ])
            ->add('lastName', TextType::class, [
                'disabled' => true,
                'attr' => [
                    'class' => 'hidden'
                ],
                'label_attr' => [
                    'class' => 'hidden'
                ]
            ])
            ->add('email', TextType::class, [
                'disabled' => true,
                'attr' => [
                    'class' => 'hidden'
                ],
                'label_attr' => [
                    'class' => 'hidden'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
