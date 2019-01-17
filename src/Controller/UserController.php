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
        if (!$this->getUser()) {
            $error = $authenticationUtils->getLastAuthenticationError();

            $lastUsername = $authenticationUtils->getLastUsername();

            $form = $this->createForm(LoginType::class, null, ['lastUsername' => $lastUsername]);

            return $this->render('user/login.html.twig', [
                'error' => $error,
                'form' => $form->createView()
            ]);
        }

        return $this->redirectToRoute('app_profile_view', [
            'id' => $this->getUser()->getId()
        ]);
    }

    /**
     * @Route("/registration", name="app_registration")
     * @param Request $request
     * @return Response
     */
    public function registrationAction(Request $request)
    {
        if (!$this->getUser()) {
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

        return $this->redirectToRoute('app_profile_view', [
            'id' => $this->getUser()->getId()
        ]);
    }
}
