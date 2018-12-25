<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Like;
use App\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/category/{id<\d+>}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categoryAction($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $articles = $category->getArticles();

        return $this->render('article/category.html.twig', [
            'articles' => $articles,
            'category' => $category
        ]);
    }

    /**
     * @Route("/tag/{id<\d+>}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tagAction($id)
    {
        $tag = $this->getDoctrine()->getRepository(Tag::class)->find($id);
        $articles = $tag->getArticles();

        return $this->render('article/tag.html.twig', [
            'articles' => $articles,
            'tag' => $tag
        ]);
    }

    public function lastAction($limit = 5)
    {
        $em = $this->getDoctrine()->getRepository(Article::class);
        $query = $em->createQueryBuilder('a')
            ->select('a.id', 'a.title')
            ->orderBy('a.created', 'DESC');
        $query->setMaxResults($limit);
        $articles = $query->getQuery()->getResult();

        return $this->render('article/last.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/archive/{year<\d+>}/{month<\d+>}")
     * @param $month
     * @param $year
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function archiveAction($month, $year)
    {
        $em = $this->getDoctrine()->getRepository(Article::class);
        $query = $em->createQueryBuilder('a')
            ->select('a')
            ->where('Month(a.created) = :month', 'Year(a.created) = :year')
            ->orderBy('a.created', 'DESC');
        $query->setParameter('month', $month);
        $query->setParameter('year', $year);
        $articles = $query->getQuery()->getResult();

        return $this->render('article/archive.html.twig', [
            'articles' => $articles,
            'year' => $year,
            'month' => $month
        ]);
    }

    /**
     * @Route("/article/{id<\d+>}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        return $this->render('article/view.html.twig', [
            'article' => $article,
        ]);
    }
}
