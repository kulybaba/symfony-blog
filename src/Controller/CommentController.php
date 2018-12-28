<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\UpdateCommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("article/{article_id<\d+>}/comment/update/{id<\d+>}")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function updateAction(Request $request, $id)
    {
        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($id);
        $comment->setUpdated(new \DateTime());

        $form = $this->createForm(UpdateCommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('notice', 'Comment updated!');

            return $this->redirectToRoute('app_article_view', [
                'id' => $comment->getArticle()->getId()
            ]);
        }

        return $this->render('comment/update.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    /**
     * @Route("/article/{article_id<\d+>}/comment/delete/{id<\d+>}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        $this->addFlash('notice', 'Comment deleted!');

        return $this->redirectToRoute('app_article_view', [
            'id' => $comment->getArticle()->getId()
        ]);
    }
}
