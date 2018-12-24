<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArchiveController extends AbstractController
{
    public function listAction()
    {
        $em = $this->getDoctrine()->getRepository(Article::class);
        $query = $em->createQueryBuilder('a')
            ->select('Month(a.created) AS month', 'Year(a.created) AS year')
            ->groupBy('month', 'year')
            ->orderBy('year', "DESC");
        $archive = $query->getQuery()->getResult();

        return $this->render('archive/list.html.twig', [
            'archive' => $archive
        ]);
    }
}
