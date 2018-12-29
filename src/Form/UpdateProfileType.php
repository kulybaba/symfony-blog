<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-group',
                ],
                'empty_data' => ''
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-group',
                ],
                'empty_data' => ''
            ])
            ->add('plainPassword', PasswordType::class, [
                'disabled' => true,
                'attr' => [
                    'class' => 'hidden'
                ],
                'label_attr' => [
                    'class' => 'hidden'
                ]
            ])
            ->add('profile', ProfileType::class, [
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
