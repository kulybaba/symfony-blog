<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Complaint;
use App\Entity\Likes;
use App\Entity\Tag;
use App\Form\ChangePictureType;
use App\Form\CreateArticleType;
use App\Form\CreateCommentType;
use App\Form\UpdateArticleType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    private const ARTICLES_LIMIT_ON_PAGE = 5;
    private const COMMENTS_LIMIT_ON_PAGE = 5;
    /**
     * @Route("/category/{id<\d+>}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categoryAction(Request $request, PaginatorInterface $paginator, $id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        $query = $this->getDoctrine()->getRepository(Article::class)->findArticlesByCategoryQuery($category->getId());

        return $this->render('article/category.html.twig', [
            'pagination' => $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                self::ARTICLES_LIMIT_ON_PAGE
            ),
            'category' => $category
        ]);
    }

    /**
     * @Route("/tag/{id<\d+>}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tagAction(Request $request, PaginatorInterface $paginator, $id)
    {
        $tag = $this->getDoctrine()->getRepository(Tag::class)->find($id);

        $query = $this->getDoctrine()->getRepository(Article::class)->findArticlesByTagQuery($tag->getId());

        return $this->render('article/tag.html.twig', [
            'pagination' => $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                self::ARTICLES_LIMIT_ON_PAGE
            ),
            'tag' => $tag
        ]);
    }

    public function lastAction()
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findLastArticles();

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
    public function archiveAction(Request $request, PaginatorInterface $paginator, $month, $year)
    {
        $query = $this->getDoctrine()->getRepository(Article::class)->findArticlesByArchiveQuery($month, $year);

        return $this->render('article/archive.html.twig', [
            'pagination' => $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                self::ARTICLES_LIMIT_ON_PAGE
            ),
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
    public function viewAction(Request $request, PaginatorInterface $paginator, $id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $tags = $article->getTag();
        $query = $this->getDoctrine()->getRepository(Comment::class)->findCommentsByArticleQuery($article->getId());

        $complaint = null;

        if ($this->getUser()) {
            $complaint = $this->getDoctrine()->getRepository(Complaint::class)->findOneBy(['article' => $article->getId(), 'author' => $this->getUser()->getId()]);
        }

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
            'form' => $form->createView(),
            'complaint' => $complaint,
            'pagination' => $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                self::COMMENTS_LIMIT_ON_PAGE
            ),
        ]);
    }

    /**
     * @Route("article/create")
     * @IsGranted("ROLE_BLOGGER")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
     * @IsGranted("ROLE_BLOGGER")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        if ($this->getUser() == $article->getAuthor() || $this->isGranted('ROLE_ADMIN')) {
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

        throw new \Exception('Access denied');
    }

    /**
     * @Route("/article/update/{id<\d+>}")
     * @IsGranted("ROLE_BLOGGER")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function updateAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $article->setUpdated(new \DateTime());

        if ($this->getUser() == $article->getAuthor() || $this->isGranted('ROLE_ADMIN')) {
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

        throw new \Exception('Access denied');
    }

    /**
     * @Route("/article/like")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function likeAction()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
     * @IsGranted("ROLE_BLOGGER")
     * @Route("/article/update/{id<\d+>}/delete-picture")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function deletePictureAction($id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        if ($this->getUser() == $article->getAuthor() || $this->isGranted('ROLE_ADMIN')) {
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

        throw new \Exception('Access denied');
    }

    /**
     * @Route("/article/update/{id<\d+>}/change-picture")
     * @IsGranted("ROLE_BLOGGER")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function changePictureAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        if ($this->getUser() == $article->getAuthor() || $this->isGranted('ROLE_ADMIN')) {
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

                return $this->redirectToRoute('app_article_view', [
                    'id' => $article->getId()
                ]);
            }

            return $this->render('article/change-picture.html.twig', [
                'form' => $form->createView(),
                'article' => $article
            ]);
        }

        throw new \Exception('Access denied');
    }
}
