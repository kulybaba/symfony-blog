<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Likes;
use App\Entity\Tag;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("admin")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $countAuthors = $this->getDoctrine()->getRepository(User::class)->getCountAuthors();
        $countArticles = $this->getDoctrine()->getRepository(Article::class)->getCountArticles();
        $countLikes = $this->getDoctrine()->getRepository(Likes::class)->getCountLikes();
        $countComments = $this->getDoctrine()->getRepository(Comment::class)->getCountComments();
        $countCategories = $this->getDoctrine()->getRepository(Category::class)->getCountCategories();
        $countTags = $this->getDoctrine()->getRepository(Tag::class)->getCountTags();

        $lastAuthors = $this->getDoctrine()->getRepository(User::class)->findLastAuthors();
        $lastArticles = $this->getDoctrine()->getRepository(Article::class)->findLastArticles();
        $lastLikes = $this->getDoctrine()->getRepository(Likes::class)->findLastLikes();
        $lastComments = $this->getDoctrine()->getRepository(Comment::class)->findLastComments();
        $lastCategories = $this->getDoctrine()->getRepository(Category::class)->findLastCategories();
        $lastTags = $this->getDoctrine()->getRepository(Tag::class)->findLastTags();

        return $this->render('admin/dashboard/index.html.twig', [
            'countAuthors' => $countAuthors,
            'countArticles' => $countArticles,
            'countLikes' => $countLikes,
            'countComments' => $countComments,
            'countCategories' => $countCategories,
            'countTags' => $countTags,

            'lastAuthors' => $lastAuthors,
            'lastArticles' => $lastArticles,
            'lastLikes' => $lastLikes,
            'lastComments' => $lastComments,
            'lastCategories' => $lastCategories,
            'lastTags' => $lastTags
        ]);
    }
}
