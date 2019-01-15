<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Form\Admin\CommentType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/list")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, PaginatorInterface $paginator)
    {
        $query = $this->getDoctrine()->getRepository(Comment::class)
            ->createQueryBuilder('c')
            ->select('c')
            ->getQuery();

        return $this->render('admin/comment/list.html.twig', [
            'pagination' => $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
            )
        ]);
    }

    /**
     * @Route("/admin/comments/create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function createAction(Request $request)
    {
        $comment = new Comment();
        $comment->setCreated(new \DateTime());
        $comment->setUpdated(new \DateTime());

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('notice', 'Comment created!');

            return $this->redirectToRoute('app_admin_comment_list');
        }

        return $this->render('admin/comment/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/comments/{id<\d+>}/update")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function updateAction(Request $request, $id)
    {
        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($id);
        $comment->setUpdated(new \DateTime());

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('notice', 'Comment updated!');

            return $this->redirectToRoute('app_admin_comment_view', [
                'id' => $comment->getId()
            ]);
        }

        return $this->render('admin/comment/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/comments/{id<\d+>}/view")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($id)
    {
        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($id);

        return $this->render('admin/comment/view.html.twig', [
            'comment' => $comment
        ]);
    }

    /**
     * @Route("/admin/comments/{id<\d+>}/delete")
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

        return $this->redirectToRoute('app_admin_comment_list');
    }
}
