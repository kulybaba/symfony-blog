<?php

namespace App\Controller\Api;

use App\Entity\Profile;
use App\Entity\User;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/api/registration", methods={"POST"})
     */
    public function registrationAction(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, UserService $userService)
    {
        if (!$request->getContent()) {
            throw new HttpException('400', 'Bad request');
        }

        $user = $serializer->deserialize($request->getContent(), User::class, JsonEncoder::FORMAT);

        if (count($validator->validate($user))) {
            throw new HttpException('400', 'Bad request');
        }

        $user->setApiToken($userService->generateApiToken());

        $profile = new Profile();
        $profile->setPicture('/images/profile/default_picture.png');
        $user->setProfile($profile);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $userService->sendRegistrationEmail($user);

        return $this->json($user);
    }

    /**
     * @Route("/api/login", methods={"POST"})
     */
    public function loginAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserService $userService)
    {
        if (!$request->getContent()) {
            throw new HttpException('400', 'Bad request');
        }

        $data = json_decode($request->getContent(), true);

        if (!array_key_exists('email', $data) || !array_key_exists('password', $data)) {
            throw new HttpException('400', 'Bad request');
        }

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        $user->setApiToken($userService->generateApiToken());

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        if ($user instanceof User) {
            if ($passwordEncoder->isPasswordValid($user, $data['password'])) {
                return $this->json($user);
            }
        }

        throw new HttpException('400', 'Bad request');
    }
}
