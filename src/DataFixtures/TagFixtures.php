<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tag = new Tag();
        $tag->setText('peoples');
        $manager->persist($tag);
        $manager->flush();

        $tag = new Tag();
        $tag->setText('news');
        $manager->persist($tag);
        $manager->flush();

        $tag = new Tag();
        $tag->setText('it');
        $manager->persist($tag);
        $manager->flush();
    }
}
