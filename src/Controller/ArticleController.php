<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Likes;
use App\Entity\Tag;
use App\Form\CreateArticleType;
use App\Form\CreateCommentType;
use App\Form\UpdateArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/category/{id<\d+>}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categoryAction($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $articles = $category->getArticles();

        return $this->render('article/category.html.twig', [
            'articles' => $articles,
            'category' => $category
        ]);
    }

    /**
     * @Route("/tag/{id<\d+>}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tagAction($id)
    {
        $tag = $this->getDoctrine()->getRepository(Tag::class)->find($id);
        $articles = $tag->getArticles();

        return $this->render('article/tag.html.twig', [
            'articles' => $articles,
            'tag' => $tag
        ]);
    }

    public function lastAction($limit = 5)
    {
        $em = $this->getDoctrine()->getRepository(Article::class);
        $query = $em->createQueryBuilder('a')
            ->select('a.id', 'a.title')
            ->orderBy('a.created', 'DESC');
        $query->setMaxResults($limit);
        $articles = $query->getQuery()->getResult();

        return $this->render('article/last.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/archive/{year<\d+>}/{month<\d+>}")
     * @param $month
     * @param $year
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function archiveAction($month, $year)
    {
        $em = $this->getDoctrine()->getRepository(Article::class);
        $query = $em->createQueryBuilder('a')
            ->select('a')
            ->where('Month(a.created) = :month', 'Year(a.created) = :year')
            ->orderBy('a.created', 'DESC');
        $query->setParameter('month', $month);
        $query->setParameter('year', $year);
        $articles = $query->getQuery()->getResult();

        return $this->render('article/archive.html.twig', [
            'articles' => $articles,
            'year' => $year,
            'month' => $month
        ]);
    }

    /**
     * @Route("/article/view/{id<\d+>}")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function viewAction(Request $request, $id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $tags = $article->getTag();

        $like = null;

        if ($this->getUser()) {
            $like = $this->getDoctrine()->getRepository(Likes::class)->findOneBy(['article' => $article->getId(), 'author' => $this->getUser()->getId()]);
        }

        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $comment->setArticle($article);
        $comment->setCreated(new \DateTime());
        $comment->setUpdated(new \DateTime());

        $form = $this->createForm(CreateCommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('notice', 'Comment created!');

            return $this->redirectToRoute('app_article_view', [
                'id' => $article->getId()
            ]);
        }

        return $this->render('article/view.html.twig', [
            'article' => $article,
            'tags' => $tags,
            'like' => $like,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("article/create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function createAction(Request $request)
    {
        $article = new Article();
        $article->setAuthor($this->getUser());
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

            return $this->redirectToRoute('app_site_index');
        }

        return $this->render('article/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/delete/{id<\d+>}")
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

        return $this->redirectToRoute('app_site_index');
    }

    /**
     * @Route("/article/update/{id<\d+>}")
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

            return $this->redirectToRoute('app_article_view', [
                'id' => $article->getId()
            ]);
        }

        return $this->render('article/update.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    /**
     * @Route("/article/like")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function likeAction()
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find(Request::createFromGlobals()->request->get('id'));
        $author = $this->getUser();

        if (!$this->getDoctrine()->getRepository(Likes::class)->findOneBy(['article' => $article->getId(), 'author' => $author->getId()])) {
            $like = new Likes();
            $like->setArticle($article);
            $like->setAuthor($author);

            $em = $this->getDoctrine()->getManager();
            $em->persist($like);
            $em->flush();

            return $this->json([
                'success' => true,
                'likesCount' => $article->getLikes()->count()
            ]);
        }
    }

    /**
     * @Route("/article/unlike")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function unlikeAction()
    {
        $id = Request::createFromGlobals()->request->get('id');

        if ($like = $this->getDoctrine()->getRepository(Likes::class)->findOneBy(['author' => $this->getUser()->getId(), 'article' => $id])) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($like);
            $em->flush();

            return $this->json([
                'success' => true,
                'likesCount' => $this->getDoctrine()->getRepository(Article::class)->find($id)->getLikes()->count()
            ]);
        }
    }

    /**
     * @Route("/article/update/{id<\d+>}/delete-picture")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePictureAction($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        //$picture = $this->file(substr($profile->getPicture(), 1))->getFile();
        $picture = $this->file(ltrim($article->getPicture(), '/'))->getFile();
        unlink($picture);

        $article->setPicture(null);

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        $this->addFlash('notice', 'Picture deleted!');

        return $this->redirectToRoute('app_article_view', [
            'id' => $article->getId()
        ]);
    }
}
