<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setFirstName('Reader');
        $user->setLastName('Reader');
        $user->setEmail('reader@mail.com');
        $user->setRoles(['ROLE_READER']);
        $user->setPlainPassword('111111');
        $profile = new Profile();
        $profile->setPicture('/images/profile/default_picture.png');
        $user->setProfile($profile);
        $manager->persist($user);
        $manager->flush();
    }
}
