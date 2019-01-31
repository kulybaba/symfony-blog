<?php

namespace App\Controller\Admin;

use App\Entity\Requests;
use App\Services\UserService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RequestsController extends AbstractController
{
    /**
     * @Route("/admin/requests/list")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, PaginatorInterface $paginator)
    {
        $query = $this->getDoctrine()->getRepository(Requests::class)->findAllRequestsQuery();

        return $this->render('admin/requests/list.html.twig', [
            'pagination' => $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
            )
        ]);
    }

    public function countAction()
    {
        $countRequests = $this->getDoctrine()->getRepository(Requests::class)->getCountRequests();

        return new Response($countRequests);
    }

    /**
     * @Route("/admin/requests/{id<\d+>}/approve")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function approveAction($id, UserService $userService)
    {
        $request = $this->getDoctrine()->getRepository(Requests::class)->find($id);

        $author = $request->getAuthor();
        $author->setRoles(['ROLE_BLOGGER']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($author);
        $em->flush();

        $em = $this->getDoctrine()->getManager();
        $em->remove($request);
        $em->flush();

        $userService->sendApproveBloggerEmail($author);

        $this->addFlash('notice', 'Request approved!');

        return $this->redirectToRoute('app_admin_requests_list');
    }

    /**
     * @Route("/admin/requests/{id<\d+>}/refuse")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function refuseAction($id, UserService $userService)
    {
        $request = $this->getDoctrine()->getRepository(Requests::class)->find($id);

        $userService->sendRefuseBloggerEmail($request->getAuthor());

        $em = $this->getDoctrine()->getManager();
        $em->remove($request);
        $em->flush();

        $this->addFlash('notice', 'Request denied!');

        return $this->redirectToRoute('app_admin_requests_list');
    }
}
