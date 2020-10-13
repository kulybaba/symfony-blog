<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Complaint;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ComplaintController extends AbstractController
{
    /**
     * @Route("/article/{id<\d+>}/complain")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function sendAction($id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $author = $this->getUser();

        if (!$this->getDoctrine()->getRepository(Complaint::class)->findOneBy(['article' => $article->getId(), 'author' => $author->getId()]))
        {
            $complaint = new Complaint();
            $complaint->setArticle($article);
            $complaint->setAuthor($author);
            $em = $this->getDoctrine()->getManager();
            $em->persist($complaint);
            $em->flush();

            $this->addFlash('notice', 'Complaint sent!');

            return $this->redirectToRoute('app_article_view', [
                'id' => $article->getId()
            ]);
        }

        throw new \Exception('Complaint already sent');
    }
}
