<?php

namespace App\Controller;

use App\Entity\Requests;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RequestsController extends AbstractController
{
    /**
     * @Route("/profile/{id<\d+>}/send-request")
     * @IsGranted("ROLE_READER")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function sendAction($id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $author = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($this->getUser() == $author || $this->isGranted('ROLE_ADMIN')) {
            $request = new Requests();
            $request->setAuthor($author);

            $em = $this->getDoctrine()->getManager();
            $em->persist($request);
            $em->flush();

            $this->addFlash('notice', 'Request on blogger sent!');

            return $this->redirectToRoute('app_profile_view', [
                'id' => $author->getId()
            ]);
        }

        throw new \Exception('Access denied');
    }
}
