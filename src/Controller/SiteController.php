<?php

namespace App\Controller;

use App\Entity\Article;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    private const ARTICLES_LIMIT_ON_PAGE = 5;

    /**
     * @Route("/")
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $query = $this->getDoctrine()->getRepository(Article::class)
            ->createQueryBuilder('a')
            ->select('a')
            ->orderBy('a.created', 'DESC')
            ->getQuery();

        return $this->render('site/index.html.twig', [
            'pagination' => $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                self::ARTICLES_LIMIT_ON_PAGE
            )
        ]);
    }
}
