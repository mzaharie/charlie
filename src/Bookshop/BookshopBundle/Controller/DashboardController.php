<?php

namespace Bookshop\BookshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bookshop\BookshopBundle\Entity\User;
use Bookshop\BookshopBundle\Entity\Address;
use Bookshop\BookshopBundle\Form\Type\AddressFormType;

class DashboardController extends Controller {

    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $orders = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->getRecentNr($user->getId(), 3);

        return $this->render('BookshopBookshopBundle:Dashboard:index.html.twig', array(
                    'orders' => $orders
                        )
        );
    }

    public function billingAddressShowAction() {
        $user = new User();
        $user = $this->get('security.context')->getToken()->getUser();
        $billingAddress = $user->getBillingAddress();

        //$em = $this->getDoctrine()->getManager();
        //$entity = $em->getRepository('BookshopBundle:Address')->find($id);

        return $this->render('BookshopBookshopBundle:Dashboard:billingAddressShow.html.twig', array(
                    'billing_address' => $billingAddress
                        )
        );
    }

    public function shippingAddressShowAction() {
        $user = new User();
        $user = $this->get('security.context')->getToken()->getUser();
        $shippingAddress = $user->getShippingAddress();

        //$em = $this->getDoctrine()->getManager();
        //$entity = $em->getRepository('BookshopBundle:Address')->find($id);

        return $this->render('BookshopBookshopBundle:Dashboard:shippingAddressShow.html.twig', array(
                    'shipping_address' => $shippingAddress
                        )
        );
    }

    public function billingAddressEditAction() {
        $user = new User();
        $user = $this->get('security.context')->getToken()->getUser();
        $billingAddress = new Address();
        if (!is_null($user->getBillingAddress())) {
            $billingAddress = $user->getBillingAddress();
        }

        $form = $this->createForm(new AddressFormType(), $billingAddress);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
        }

        if ($form->isValid()) {
            $em = $this->getDoctrine()
                    ->getManager();
            if (!is_null($user->getShippingAddress() && $billingAddress->getId() == $user->getShippingAddress()->getId())) {

                $newAddress = new Address();
                $newAddress->copy($billingAddress);
                $billingAddress = $newAddress;
            }

            $em->persist($billingAddress);
            $em->flush($billingAddress);

            $user->setBillingAddress($billingAddress);
            $em->persist($user);
            $em->flush($user);

            return $this->redirect($this->generateUrl('dashboard_index'));
        }

        return $this->render('BookshopBookshopBundle:Dashboard:billingAddressEdit.html.twig', array(
                    'form' => $form->createView()
                        )
        );
    }

    public function billingAddressPreEditAction() {
        $user = new User();
        $user = $this->get('security.context')->getToken()->getUser();
        $billingAddress = new Address();
        if (!is_null($user->getBillingAddress())) {
            $billingAddress = $user->getBillingAddress();
        }

        return $this->render('BookshopBookshopBundle:Dashboard:billingAddrPreEdit.html.twig', array(
                    'billing_address' => $billingAddress
                        )
        );
    }

    public function shippingAddressEditAction() {
        $user = new User();
        $user = $this->get('security.context')->getToken()->getUser();
        $shippingAddress = new Address();
        if (!is_null($user->getShippingAddress())) {
            $shippingAddress = $user->getShippingAddress();
        }

        $form = $this->createForm(new AddressFormType(), $shippingAddress);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
        }

        if ($form->isValid()) {
            $em = $this->getDoctrine()
                    ->getManager();
            if ($shippingAddress->getId() == $user->getBillingAddress()->getId() && !is_null($user->getBillingAddress())) {

                $newAddress = new Address();
                $newAddress->copy($shippingAddress);
                $shippingAddress = $newAddress;
            }

            $em->persist($shippingAddress);
            $em->flush($shippingAddress);

            $user->setShippingAddress($shippingAddress);
            $em->persist($user);
            $em->flush($user);

            return $this->redirect($this->generateUrl('dashboard_index'));
        }

        return $this->render('BookshopBookshopBundle:Dashboard:shippingAddressEdit.html.twig', array(
                    'form' => $form->createView()
                        )
        );
    }

    public function shippingAddressPreEditAction() {
        $user = new User();
        $user = $this->get('security.context')->getToken()->getUser();
        $shippingAddress = new Address();
        if (!is_null($user->getShippingAddress())) {
            $shippingAddress = $user->getShippingAddress();
        }

        return $this->render('BookshopBookshopBundle:Dashboard:shippingAddrPreEdit.html.twig', array(
                    'shipping_address' => $shippingAddress
                        )
        );
    }

    public function ordersviewAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->getUserOrders($user->getId());

        $paginator = $this->get('knp_paginator');

        $orders = $paginator->paginate($query, $this->get('request')->query->get('page', 1), 10);

        return $this->render('BookshopBookshopBundle:Dashboard:orders.html.twig', array(
                    'orders' => $orders
                        )
        );
    }

    public function vieworderAction() {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $order = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->viewOrder($request->request->get('id'));
        $referer = $this->getRequest()->headers->get('referer');

        return $this->render('BookshopBookshopBundle:Dashboard:vieworder.html.twig', array(
                    'order' => $order,
                    'referrer' => $referer
                        )
        );
    }

}
