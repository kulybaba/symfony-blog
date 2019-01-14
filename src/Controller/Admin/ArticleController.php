<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\Admin\CreateArticleType;
use App\Form\Admin\UpdateArticleType;
use App\Form\ChangePictureType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/admin/articles/list")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, PaginatorInterface $paginator)
    {
        $query = $this->getDoctrine()->getRepository(Article::class)
            ->createQueryBuilder('a')
            ->select('a')
            ->getQuery();

        return $this->render('admin/article/list.html.twig', [
            'pagination' => $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
            )
        ]);
    }

    /**
     * @Route("/admin/articles/create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function createAction(Request $request)
    {
        $article = new Article();
        $article->setCreated(new \DateTime());
        $article->setUpdated(new \DateTime());

        $form = $this->createForm(CreateArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($_FILES['create_article']['tmp_name']['picture']) {
                $picture = $this->file($_FILES['create_article']['tmp_name']['picture'])->getFile();
                $pictureName = md5(uniqid()) . '.' . $picture->guessExtension();
                $picture->move('uploads/article/', $pictureName);
                $article->setPicture('/uploads/article/' . $pictureName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('notice', 'Article created!');

            return $this->redirectToRoute('app_admin_article_list');
        }

        return $this->render('admin/article/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/articles/{id<\d+>}/update")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function updateAction(Request $request, $id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $article->setUpdated(new \DateTime());

        $form = $this->createForm(UpdateArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('notice', 'Article updated!');

            return $this->redirectToRoute('app_admin_article_list');
        }

        return $this->render('admin/article/update.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/articles/{id<\d+>}/delete")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        if ($article->getPicture()) {
            //$picture = $this->file(substr($profile->getPicture(), 1))->getFile();
            $picture = $this->file(ltrim($article->getPicture(), '/'))->getFile();
            unlink($picture);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        $this->addFlash('notice', 'Article deleted!');

        return $this->redirectToRoute('app_admin_article_list');
    }

    /**
     * @Route("/admin/articles/{id<\d+>}/view")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        return $this->render('admin/article/view.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("admin/articles/{id<\d+>}/change-picture")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function changePictureAction(Request $request, $id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        $form = $this->createForm(ChangePictureType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($article->getPicture()) {
                //$oldPicture = $this->file(substr($profile->getPicture(), 1))->getFile();
                $oldPicture = $this->file(ltrim($article->getPicture(), '/'))->getFile();
                unlink($oldPicture);
            }

            $picture = $this->file($_FILES['change_picture']['tmp_name']['picture'])->getFile();
            $pictureName = md5(uniqid()) . '.' . $picture->guessExtension();
            $picture->move('uploads/article/', $pictureName);

            $article->setPicture('/uploads/article/' . $pictureName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('notice', 'Picture changed!');

            return $this->redirectToRoute('app_admin_article_view', [
                'id' => $article->getId()
            ]);
        }

        return $this->render('admin/article/change-picture.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    /**
     * @Route("admin/articles/{id<\d+>}/delete-picture")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePictureAction($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        if ($article->getPicture()) {
            //$picture = $this->file(substr($profile->getPicture(), 1))->getFile();
            $picture = $this->file(ltrim($article->getPicture(), '/'))->getFile();
            unlink($picture);

            $article->setPicture(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('notice', 'Picture deleted!');
        }

        return $this->redirectToRoute('app_admin_article_view', [
            'id' => $article->getId()
        ]);
    }
}
