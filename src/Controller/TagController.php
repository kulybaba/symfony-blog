<?php

namespace App\Controller;

use App\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagController extends AbstractController
{
    public function listAction()
    {
        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();

        return $this->render('tag/list.html.twig', [
            'tags' => $tags
        ]);
    }
}
