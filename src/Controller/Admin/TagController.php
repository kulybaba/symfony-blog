<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Form\Admin\TagType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    /**
     * @Route("/admin/tags/list")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, PaginatorInterface $paginator)
    {
        $query = $this->getDoctrine()->getRepository(Tag::class)
            ->createQueryBuilder('t')
            ->select('t')
            ->getQuery();

        return $this->render('admin/tag/list.html.twig', [
            'pagination' => $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
            )
        ]);
    }

    /**
     * @Route("/admin/tags/create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $tag = new Tag();

        $form = $this->createForm(TagType::class, $tag);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            $this->addFlash('notice', 'Tag created!');

            return $this->redirectToRoute('app_admin_tag_list');
        }

        return $this->render('admin/tag/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/tags/{id<\d+>}/update")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id)
    {
        $tag = $this->getDoctrine()->getRepository(Tag::class)->find($id);

        $form = $this->createForm(TagType::class, $tag);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            $this->addFlash('notice', 'Tag updated!');

            return $this->redirectToRoute('app_admin_tag_view', [
                'id' => $tag->getId()
            ]);
        }

        return $this->render('admin/tag/update.html.twig', [
            'tag' => $tag,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/tags/{id<\d+>}/view")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($id)
    {
        $tag = $this->getDoctrine()->getRepository(Tag::class)->find($id);

        return $this->render('admin/tag/view.html.twig', [
            'tag' => $tag
        ]);
    }

    /**
     * @Route("/admin/tags/{id<\d+>}/delete")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $tag = $this->getDoctrine()->getRepository(Tag::class)->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($tag);
        $em->flush();

        $this->addFlash('notice', 'Tag deleted!');

        return $this->redirectToRoute('app_admin_tag_list');
    }
}
