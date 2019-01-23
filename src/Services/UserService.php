<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserService extends AbstractController
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendRegistrationEmail(User $user)
    {
        $message = (new \Swift_Message('Symfony Blog (Registration email)'))
            ->setFrom('ahurtep@gmai.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig',
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
                    'emails/registration.txt.twig',
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
                    'emails/request-approved.html.twig',
                    [
                        'firstName' => $author->getFirstName(),
                        'lastName' => $author->getLastName(),
                    ]
                ),
                'text/html'
            )
            ->addPart(
                $this->renderView(
                    'emails/request-approved.txt.twig',
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
                    'emails/request-denied.html.twig',
                    [
                        'firstName' => $author->getFirstName(),
                        'lastName' => $author->getLastName(),
                    ]
                ),
                'text/html'
            )
            ->addPart(
                $this->renderView(
                    'emails/request-denied.txt.twig',
                    [
                        'firstName' => $author->getFirstName(),
                        'lastName' => $author->getLastName(),
                    ]
                ),
                'text/plain'
            );
        $this->mailer->send($message);
    }
}
