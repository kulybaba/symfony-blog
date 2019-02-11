<?php

namespace App\Controller\Api;

use App\Entity\Article;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/api/articles/list", methods={"GET"})
     */
    public function listAction(Request $request, PaginatorInterface $paginator)
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $query = $this->getDoctrine()->getRepository(Article::class)->findAllArticlesQuery();

        return $this->json([
            'articles' => $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
            )
        ]);
    }

    /**
     * @Route("/api/articles/{id<\d+>}/view", methods={"GET"})
     */
    public function viewAction(Article $article)
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->json($article);
    }
}
