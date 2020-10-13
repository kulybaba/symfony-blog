<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Profile;
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

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $blogger = new User();
        $blogger->setFirstName('Blogger');
        $blogger->setLastName('Blogger');
        $blogger->setEmail('blogger@mail.com');
        $blogger->setRoles(['ROLE_BLOGGER']);
        $blogger->setPlainPassword('111111');
        $blogger->setApiToken('api1');
        $profile = new Profile();
        $profile->setPicture('/images/profile/default_picture.png');
        $blogger->setProfile($profile);
        $manager->persist($blogger);
        $manager->flush();

        $admin = new User();
        $admin->setFirstName('Admin');
        $admin->setLastName('Admin');
        $admin->setEmail('admin@mail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPlainPassword('111111');
        $admin->setApiToken('api2');
        $profile = new Profile();
        $profile->setPicture('/images/profile/default_picture.png');
        $admin->setProfile($profile);
        $manager->persist($admin);
        $manager->flush();

        $category = new Category();
        $category->setName('News');
        $manager->persist($category);
        $manager->flush();

        $tag1 = new Tag();
        $tag1->setText('article');
        $manager->persist($tag1);
        $manager->flush();

        $tag2 = new Tag();
        $tag2->setText('people');
        $manager->persist($tag2);
        $manager->flush();

        $tag3 = new Tag();
        $tag3->setText('news');
        $manager->persist($tag3);
        $manager->flush();

        $article1 = new Article();
        $article1->setTitle('Belgium PM Charles Michel resigns after government collapses in dispute over UN migration pact');
        $article1->setShortText('Charles Michel, the Belgian prime minister, resigned on Tuesday night after his government collapsed in the face of virulent
            opposition to his signing of a UN migration pact from his erstwhile coalition partners. Mr Michel lost the support of the Flemish nationalist N-VA, the
            largest party in his coalition, over the non-binding UN agreement, which opponents had claimed would open the door to greater migration.');
        $article1->setText('Charles Michel, the Belgian prime minister, resigned on Tuesday night after his government collapsed in the face of virulent
            opposition to his signing of a UN migration pact from his erstwhile coalition partners. Mr Michel lost the support of the Flemish nationalist N-VA, the
            largest party in his coalition, over the non-binding UN agreement, which opponents had claimed would open the door to greater migration. Belgium is now
            bracing itself for a snap election as early as next month after Mr Michel said he was going to the king to offer his resignation amid demands for a motion
            of no confidence in his now minority government. He had refused to submit to such a vote or the calls from some in the assembly for an early election.
            A snap poll, he said, would only lead to "stagnation for the whole of 2019". The next election is due in Belgium in May. Instead, Mr Michel announced:
            "I am taking the decision to offer my resignation. I am now going to see the king. Amid applause from parlamentarians, he picked up his briefcase, shook
            the hands of a number of government ministers, and left. King Philippe of Belgium received Michel and is now expected to hold consultations between the
            political parties before calling elections in January.');
        $article1->setAuthor($blogger);
        $article1->setCategory($category);
        $article1->addTag($tag1)->addTag($tag2)->addTag($tag3);
        $article1->setCreated(new \DateTime());
        $article1->setUpdated(new \DateTime());
        $manager->persist($article1);
        $manager->flush();

        $article2 = new Article();
        $article2->setTitle('Belgium PM Charles Michel resigns after government collapses in dispute over UN migration pact');
        $article2->setShortText('Charles Michel, the Belgian prime minister, resigned on Tuesday night after his government collapsed in the face of virulent
            opposition to his signing of a UN migration pact from his erstwhile coalition partners. Mr Michel lost the support of the Flemish nationalist N-VA, the
            largest party in his coalition, over the non-binding UN agreement, which opponents had claimed would open the door to greater migration.');
        $article2->setText('Charles Michel, the Belgian prime minister, resigned on Tuesday night after his government collapsed in the face of virulent
            opposition to his signing of a UN migration pact from his erstwhile coalition partners. Mr Michel lost the support of the Flemish nationalist N-VA, the
            largest party in his coalition, over the non-binding UN agreement, which opponents had claimed would open the door to greater migration. Belgium is now
            bracing itself for a snap election as early as next month after Mr Michel said he was going to the king to offer his resignation amid demands for a motion
            of no confidence in his now minority government. He had refused to submit to such a vote or the calls from some in the assembly for an early election.
            A snap poll, he said, would only lead to "stagnation for the whole of 2019". The next election is due in Belgium in May. Instead, Mr Michel announced:
            "I am taking the decision to offer my resignation. I am now going to see the king. Amid applause from parlamentarians, he picked up his briefcase, shook
            the hands of a number of government ministers, and left. King Philippe of Belgium received Michel and is now expected to hold consultations between the
            political parties before calling elections in January.');
        $article2->setAuthor($admin);
        $article2->setCategory($category);
        $article2->addTag($tag1);
        $article2->setCreated(new \DateTime());
        $article2->setUpdated(new \DateTime());
        $manager->persist($article2);
        $manager->flush();

        $comment = new Comment();
        $comment->setText('Fine!');
        $comment->setAuthor($blogger);
        $comment->setArticle($article1);
        $comment->setCreated(new \DateTime());
        $comment->setUpdated(new \DateTime());
        $manager->persist($comment);
        $manager->flush();

        $comment = new Comment();
        $comment->setText('Good');
        $comment->setAuthor($admin);
        $comment->setArticle($article1);
        $comment->setCreated(new \DateTime());
        $comment->setUpdated(new \DateTime());
        $manager->persist($comment);
        $manager->flush();

        $comment = new Comment();
        $comment->setText(':-D');
        $comment->setAuthor($admin);
        $comment->setArticle($article2);
        $comment->setCreated(new \DateTime());
        $comment->setUpdated(new \DateTime());
        $manager->persist($comment);
        $manager->flush();
    }
}
