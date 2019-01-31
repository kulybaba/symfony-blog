<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService extends AbstractController
{
    private $mailer;

    private $passwordEncoder;

    public function __construct(\Swift_Mailer $mailer, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->mailer = $mailer;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function encodePassword(User $user)
    {
        return $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
    }

    public function sendPasswordChangeEmail(User $user)
    {
        $message = (new \Swift_Message('Symfony Blog (Email about change password)'))
            ->setFrom('ahurtep@gmai.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/change-password.html.twig',
                    [
                        'firstName' => $user->getFirstName(),
                        'lastName' => $user->getLastName(),
                        'email' => $user->getEmail(),
                        'password' => $user->getPlainPassword(),
                    ]
                ),
                'text/html'
            )
            ->addPart(
                $this->renderView(
                    'emails/change-password.txt.twig',
                    [
                        'firstName' => $user->getFirstName(),
                        'lastName' => $user->getLastName(),
                        'email' => $user->getEmail(),
                        'password' => $user->getPlainPassword(),
                    ]
                ),
                'text/plain'
            );
        $this->mailer->send($message);
    }
}
