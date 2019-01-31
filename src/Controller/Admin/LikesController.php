<?php

namespace App\Controller\Admin;

use App\Entity\Likes;
use App\Form\Admin\LikesType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LikesController extends AbstractController
{
    /**
     * @Route("/admin/likes/list")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, PaginatorInterface $paginator)
    {
        $query = $this->getDoctrine()->getRepository(Likes::class)->findAllLikesQuery();

        return $this->render('admin/likes/list.html.twig', [
            'pagination' => $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
            )
        ]);
    }

    /**
     * @Route("/admin/likes/create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $like = new Likes();

        $form = $this->createForm(LikesType::class, $like);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($like);
            $em->flush();

            $this->addFlash('notice', 'Like created!');

            return $this->redirectToRoute('app_admin_likes_list');
        }

        return $this->render('admin/likes/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/likes/{id<\d+>}/update")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id)
    {
        $like = $this->getDoctrine()->getRepository(Likes::class)->find($id);

        $form = $this->createForm(LikesType::class, $like);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($like);
            $em->flush();

            $this->addFlash('notice', 'Like updated!');

            return $this->redirectToRoute('app_admin_likes_view', [
                'id' => $like->getId()
            ]);
        }

        return $this->render('admin/likes/update.html.twig', [
            'like' => $like,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/likes/{id<\d+>}/view")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($id)
    {
        $like = $this->getDoctrine()->getRepository(Likes::class)->find($id);

        return $this->render('admin/likes/view.html.twig', [
            'like' => $like
        ]);
    }

    /**
     * @Route("/admin/likes/{id<\d+>}/delete")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $like = $this->getDoctrine()->getRepository(Likes::class)->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($like);
        $em->flush();

        $this->addFlash('notice', 'Request deleted!');

        return $this->redirectToRoute('app_admin_likes_list');
    }
}
