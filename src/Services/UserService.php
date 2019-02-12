<?php

namespace App\Services;

use App\Entity\User;
use Ramsey\Uuid\Uuid;
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

    public function sendRegistrationEmail(User $user)
    {
        $message = (new \Swift_Message('Symfony Blog (Registration email)'))
            ->setFrom('ahurtep@gmai.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'user/emails/registration.html.twig',
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
                    'user/emails/registration.txt.twig',
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

    public function sendApproveBloggerEmail(User $author)
    {
        $message = (new \Swift_Message('Symfony Blog (Request approved)'))
            ->setFrom('ahurtep@gmai.com')
            ->setTo($author->getEmail())
            ->setBody(
                $this->renderView(
                    'user/emails/request-approved.html.twig',
                    [
                        'firstName' => $author->getFirstName(),
                        'lastName' => $author->getLastName(),
                    ]
                ),
                'text/html'
            )
            ->addPart(
                $this->renderView(
                    'user/emails/request-approved.txt.twig',
                    [
                        'firstName' => $author->getFirstName(),
                        'lastName' => $author->getLastName(),
                    ]
                ),
                'text/plain'
            );
        $this->mailer->send($message);
    }

    public function sendRefuseBloggerEmail(User $author)
    {
        $message = (new \Swift_Message('Symfony Blog (Request denied!)'))
            ->setFrom('ahurtep@gmai.com')
            ->setTo($author->getEmail())
            ->setBody(
                $this->renderView(
                    'user/emails/request-denied.html.twig',
                    [
                        'firstName' => $author->getFirstName(),
                        'lastName' => $author->getLastName(),
                    ]
                ),
                'text/html'
            )
            ->addPart(
                $this->renderView(
                    'user/emails/request-denied.txt.twig',
                    [
                        'firstName' => $author->getFirstName(),
                        'lastName' => $author->getLastName(),
                    ]
                ),
                'text/plain'
            );
        $this->mailer->send($message);
    }

    public function sendPasswordChangeEmail(User $user)
    {
        $message = (new \Swift_Message('Symfony Blog (Email about change password)'))
            ->setFrom('ahurtep@gmai.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'user/emails/change-password.html.twig',
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
                    'user/emails/change-password.txt.twig',
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

    public function generateApiToken()
    {
        return Uuid::uuid4()->toString();
    }
}
