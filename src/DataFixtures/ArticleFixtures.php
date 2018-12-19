<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ArticleFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setFirstName('John');
        $user->setLastName('Coco');
        $user->setEmail('some2@email.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordEncoder->encodePassword($user, '111111'));

        $category = new Category();
        $category->setName('Story');

        $tag = new Tag();
        $tag->setText('article');

        $article = new Article();
        $article->setTitle('Belgium PM Charles Michel resigns after government collapses in dispute over UN migration pact');
        $article->setShortText('Charles Michel, the Belgian prime minister, resigned on Tuesday night after his government collapsed in the face of virulent
            opposition to his signing of a UN migration pact from his erstwhile coalition partners. Mr Michel lost the support of the Flemish nationalist N-VA, the
            largest party in his coalition, over the non-binding UN agreement, which opponents had claimed would open the door to greater migration. Belgium is now
            bracing itself for a snap election as early as next month after Mr Michel said he was going to the king to offer his resignation amid demands for a motion
            of no confidence in his now minority government.');
        $article->setText('Belgium PM Charles Michel resigns after government collapses in dispute over UN migration pact');
        $article->setShortText('Charles Michel, the Belgian prime minister, resigned on Tuesday night after his government collapsed in the face of virulent
            opposition to his signing of a UN migration pact from his erstwhile coalition partners. Mr Michel lost the support of the Flemish nationalist N-VA, the
            largest party in his coalition, over the non-binding UN agreement, which opponents had claimed would open the door to greater migration. Belgium is now
            bracing itself for a snap election as early as next month after Mr Michel said he was going to the king to offer his resignation amid demands for a motion
            of no confidence in his now minority government. He had refused to submit to such a vote or the calls from some in the assembly for an early election.
            A snap poll, he said, would only lead to "stagnation for the whole of 2019". The next election is due in Belgium in May. Instead, Mr Michel announced:
            "I am taking the decision to offer my resignation. I am now going to see the king. Amid applause from parlamentarians, he picked up his briefcase, shook
            the hands of a number of government ministers, and left. King Philippe of Belgium received Michel and is now expected to hold consultations between the
            political parties before calling elections in January.');
        $article->setAuthor($user);
        $article->setCategory($category);
        $article->addTag($tag);

        $manager->persist($article);
        $manager->persist($user);
        $manager->persist($category);
        $manager->persist($tag);
        $manager->flush();
    }
}
