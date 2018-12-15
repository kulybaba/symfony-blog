<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $text = $crawler->filter('body > form > h1')->text();
        $this->assertEquals('Please sign in', $text);
        $this->assertCount(3, $crawler->filter('input'));
        $form = $crawler->filter('button[type=submit]')->form();
        $form['email'] = 'some@email.com';
        $form['password'] = '11111111';
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testRegistration()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/registration');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $text = $crawler->filter('body > form > h1')->text();
        $this->assertEquals('Registration', $text);
        $this->assertCount(6, $crawler->filter('input'));
        $form = $crawler->filter('button[type=submit]')->form();
        $form['registration[firstName]'] = 'Peter1';
        $form['registration[lastName]'] = 'Parker2';
        $form['registration[email]'] = 'some1@email.com';
        $form['registration[plainPassword][first]'] = '11111111';
        $form['registration[plainPassword][second]'] = '11111111';
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
