<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArchiveController extends AbstractController
{
    public function listAction()
    {
        $archive = $this->getDoctrine()->getRepository(Article::class)->findArchiveList();

        return $this->render('user/archive/list.html.twig', [
            'archive' => $archive
        ]);
    }
}
