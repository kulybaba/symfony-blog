<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\UpdateCommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("article/{article_id<\d+>}/comment/update/{id<\d+>}")
     * @IsGranted("ROLE_BLOGGER")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function updateAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($id);
        $comment->setUpdated(new \DateTime());

        if ($this->getUser() == $comment->getAuthor() || $this->isGranted('ROLE_ADMIN')) {
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

            return $this->render('user/comment/update.html.twig', [
                'form' => $form->createView(),
                'comment' => $comment
            ]);
        }

        throw new \Exception('Access denied');
    }

    /**
     * @Route("/article/{article_id<\d+>}/comment/delete/{id<\d+>}")
     * @IsGranted("ROLE_BLOGGER")@throws \Exception
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($id);

        if ($this->getUser() == $comment->getAuthor() || $this->isGranted('ROLE_ADMIN')) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();

            $this->addFlash('notice', 'Comment deleted!');

            return $this->redirectToRoute('app_article_view', [
                'id' => $comment->getArticle()->getId()
            ]);
        }

        throw new \Exception('Access denied');
    }
}
