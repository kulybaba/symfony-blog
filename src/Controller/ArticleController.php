<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/category/{id<\d+>}")
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
}
