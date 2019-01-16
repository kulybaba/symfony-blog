<?php

namespace App\Controller\Admin;

use App\Entity\Complaint;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComplaintController extends AbstractController
{
    /**
     * @Route("/admin/complaints/list")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function listAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getRepository(Complaint::class);

        $query = $em->createQueryBuilder('c')
            ->select('c')
            ->orderBy('c.id', 'DESC')
            ->getQuery();

        return $this->render('admin/complaint/list.html.twig', [
            'pagination' => $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
            )
        ]);
    }

    public function countAction()
    {
        //$countComplaints = $this->getDoctrine()->getRepository(Complaint::class)->getCountComplaints();
        $countComplaints = $this->getDoctrine()->getRepository(Complaint::class)
            ->createQueryBuilder('c')
            ->join('c.article', 'a')
            ->select('a.id')
            ->distinct()
            ->getQuery()
            ->getResult()
            ;

        return new Response(count($countComplaints));
    }

    /**
     * @Route("/admin/complaints/{id<\d+>}/approve")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function approveAction($id)
    {
        $complaint = $this->getDoctrine()->getRepository(Complaint::class)->find($id);

        $article = $complaint->getArticle();

        foreach ($this->getDoctrine()->getRepository(Complaint::class)->findAll() as $complaint)
        {
            if ($complaint->getArticle()->getId() == $article->getId()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($complaint);
                $em->flush();
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        $this->addFlash('notice', 'Complaint approved!');

        return $this->redirectToRoute('app_admin_complaint_list');
    }

    /**
     * @Route("/admin/complaints/{id<\d+>}/refuse")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function refuseAction($id)
    {
        $complaint = $this->getDoctrine()->getRepository(Complaint::class)->find($id);

        foreach ($this->getDoctrine()->getRepository(Complaint::class)->findAll() as $compl)
        {
            if ($compl->getArticle()->getId() == $complaint->getArticle()->getId()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($compl);
                $em->flush();
            }
        }

        $this->addFlash('notice', 'Complaint denied!');

        return $this->redirectToRoute('app_admin_complaint_list');
    }
}
