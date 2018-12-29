<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = new User();
        $user->setEmail($lastUsername);

        $form = $this->createForm(LoginType::class, $user);

        return $this->render('user/login.html.twig', [
            'error' => $error,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/registration", name="app_registration")
     * @param Request $request
     * @return Response
     */
    public function registrationAction(Request $request)
    {
        $user = new User();
        $profile = new Profile();
        $profile->setPicture('/images/profile/default_picture.png');
        $user->setProfile($profile);

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
