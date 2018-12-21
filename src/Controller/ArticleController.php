<?php

namespace App\Controller;

use App\Entity\Category;
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
}
