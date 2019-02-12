<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\Comment;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/api/articles/{id<\d+>}/comments/list", methods={"GET"})
     */
    public function listAction(Request $request, PaginatorInterface $paginator, Article $article)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $query = $this->getDoctrine()->getRepository(Comment::class)->findCommentsByArticleQuery($article->getId());

        return $this->json([
            'articles' => $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
            )
        ]);
    }

    /**
     * @Route("/api/articles/comments/{id<\d+>}/view", methods={"GET"})
     */
    public function viewAction(Comment $comment)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->json($comment);
    }
}
