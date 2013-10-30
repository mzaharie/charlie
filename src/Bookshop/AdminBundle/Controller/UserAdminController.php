<?php

namespace Bookshop\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Bookshop\AdminBundle\Form\Type\UserEditFormType;

/**
 * Description of UserAdminController
 *
 * @author mzaharie
 */
class UserAdminController extends Controller {

    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('BookshopBookshopBundle:User')->getUsers();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $this->get('request')->query->get('page', 1)/* page number */, 2/* limit per page */
        );
        return $this->render('BookshopAdminBundle:UserAdmin:index.html.twig', array('users' => $pagination));
    }

    public function enableAction($userID) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("BookshopBookshopBundle:User")->find($userID);
        $user->setEnabled(1);
        $em->persist($user);
        $em->flush($user);
        $url = $this->getRequest()->headers->get("referer");
        return new RedirectResponse($url);
    }

    public function disableAction($userID) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("BookshopBookshopBundle:User")->find($userID);
        $user->setEnabled(0);
        $em->persist($user);
        $em->flush($user);
        $url = $this->getRequest()->headers->get("referer");
        return new RedirectResponse($url);
    }

    public function editAction($userID) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("BookshopBookshopBundle:User")->find($userID);
        $form = $this->createForm(
                new UserEditFormType(), $user, array('validation_groups' => array('Edit'))
        );

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
        }
        if ($form->isValid()) {
            $em->persist($user);
            $em->flush($user);
            return $this->redirect($this->generateUrl('bookshop_admin_user_list'));
        }

        return $this->render('BookshopAdminBundle:UserAdmin:edit.html.twig', array('form' => $form->createView(), 'userID' => $user->getId()));
    }

    public function newuserAction() {
        $em = $this->getDoctrine()->getManager();
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $user = new \Bookshop\BookshopBundle\Entity\User();
        $form = $this->createForm(
                new \Bookshop\AdminBundle\Form\Type\UserNewFormType(), $user, array('validation_groups' => array('New'))
        );

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $user->setEnabled(true);
//                $user->setPlainPassword($form->getData()->getpassword());
                var_dump($user);
                $message = \Swift_Message::newInstance()
                        ->setSubject('New account on SymBookShop')
                        ->setFrom('symbookshop@bookshop.com')
                        ->setTo('ahusar@pitechnologies.ro')
                        ->setBody("Hello " . $user->getFirstName() . " " . $user->getLastName() . ".  A new account has been created for you on SymBookshop, using the next email address: " .$user->getEmail(). ".
                        Your username is: " . $user->getUsername() . " and your password is " . $user->getPassword() . ".
                            You can now log in on our website ( http://symbookshop/app.php/login  ) and change your password.
                            Welcome!"
                        )
                ;
                $this->get('mailer')->send($message);

                $em->persist($user);
                $em->flush($user);

                return $this->redirect($this->generateUrl('bookshop_admin_user_list'));
            }
        }


        return $this->render('BookshopAdminBundle:UserAdmin:new.html.twig', array('form' => $form->createView()));
    }

}

?>
