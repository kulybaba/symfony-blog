<?php

namespace App\Controller\Admin;

use App\Entity\Profile;
use App\Entity\User;
use App\Form\Admin\AuthorType;
use App\Form\ChangePictureType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    /**
     * @Route("/admin/authors/list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, PaginatorInterface $paginator)
    {
        $query = $this->getDoctrine()->getRepository(User::class)
            ->createQueryBuilder('a')
            ->select('a')
            ->getQuery();

        return $this->render('admin/author/list.thml.twig', [
            'pagination' => $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
            )
        ]);
    }

    /**
     * @Route("/admin/authors/create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $author = new User();
        $profile = new Profile();
        $profile->setPicture('/images/profile/default_picture.png');
        $author->setProfile($profile);

        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            $this->addFlash('notice', 'Author created!');

            return $this->redirectToRoute('app_admin_author_list');
        }

        return $this->render('admin/author/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/authors/{id<\d+>}/update")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id)
    {
        $author = $this->getDoctrine()->getRepository(User::class)->find($id);

        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            $this->addFlash('notice', 'Author updated!');

            return $this->redirectToRoute('app_admin_author_view', [
                'id' => $author->getId()
            ]);
        }

        return $this->render('admin/author/update.html.twig', [
            'author' => $author,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("admin/authors/{id<\d+>}/delete")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $author = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($author->getProfile()->getPicture() != '/images/profile/default_picture.png') {
            //$picture = $this->file(substr($profile->getPicture(), 1))->getFile();
            $picture = $this->file(ltrim($author->getPicture(), '/'))->getFile();
            unlink($picture);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($author);
        $em->flush();

        $this->addFlash('notice', 'Author deleted!');

        return $this->redirectToRoute('app_admin_author_list');
    }

    /**
     * @Route("admin/authors/{id<\d+>}/change-photo")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function changePictureAction(Request $request, $id)
    {
        $author = $this->getDoctrine()->getRepository(User::class)->find($id);
        $profile = $author->getProfile();

        $form = $this->createForm(ChangePictureType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($profile->getPicture() != '/images/profile/default_picture.png') {
                //$oldPicture = $this->file(substr($profile->getPicture(), 1))->getFile();
                $oldPicture = $this->file(ltrim($profile->getPicture(), '/'))->getFile();
                unlink($oldPicture);
            }

            $picture = $this->file($_FILES['change_picture']['tmp_name']['picture'])->getFile();
            $pictureName = md5(uniqid()) . '.' . $picture->guessExtension();
            $picture->move('uploads/profile/', $pictureName);

            $profile->setPicture('/uploads/profile/' . $pictureName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($profile);
            $em->flush();

            $this->addFlash('notice', 'Photo changed!');

            return $this->redirectToRoute('app_admin_author_view', [
                'id' => $author->getId()
            ]);
        }

        return $this->render('admin/author/change-picture.html.twig', [
            'form' => $form->createView(),
            'author' => $author
        ]);
    }

    /**
     * @Route("admin/authors/{id<\d+>}/delete-photo")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function deletePictureAction($id)
    {
        $author = $this->getDoctrine()->getRepository(User::class)->find($id);
        $profile = $author->getProfile();


        if ($profile->getPicture() != '/images/profile/default_picture.png') {
            //$picture = $this->file(substr($profile->getPicture(), 1))->getFile();
            $picture = $this->file(ltrim($profile->getPicture(), '/'))->getFile();
            unlink($picture);

            $profile->setPicture('/images/profile/default_picture.png');

            $em = $this->getDoctrine()->getManager();
            $em->persist($profile);
            $em->flush();

            $this->addFlash('notice', 'Photo deleted!');
        }

        return $this->redirectToRoute('app_admin_author_view', [
            'id' => $author->getId()
        ]);
    }

    /**
     * @Route("admin/authors/{id<\d+>}/view")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($id)
    {
        $author = $this->getDoctrine()->getRepository(User::class)->find($id);

        return $this->render('admin/author/view.html.twig', [
            'author' => $author
        ]);
    }
}
