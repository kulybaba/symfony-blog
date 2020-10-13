<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{
    public function testGetFistName()
    {
        $user = new User();

        $user->setFirstName('Peter');

        $result = $user->getFirstName();

        $this->assertEquals('Peter', $result);
    }

    public function testGetLastName()
    {
        $user = new User();

        $user->setLastName('Parker');

        $result = $user->getLastName();

        $this->assertEquals('Parker', $result);
    }

    public function testGetEmail()
    {
        $user = new User();

        $user->setEmail('some@email.com');

        $result = $user->getEmail();

        $this->assertEquals('some@email.com', $result);
    }

    public function testGetPassword()
    {
        $user = new User();

        $user->setPassword('111111');

        $result = $user->getPassword();

        $this->assertEquals('111111', $result);
    }

    public function testGetPlainPassword()
    {
        $user = new User();

        $user->setPlainPassword('111111');

        $result = $user->getPlainPassword();

        $this->assertEquals('111111', $result);
    }
}
