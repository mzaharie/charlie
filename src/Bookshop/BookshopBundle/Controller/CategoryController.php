<?php

namespace Bookshop\BookshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller {

    public function showAction($id, Request $request) {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('BookshopBookshopBundle:Category')->getCategory($id);
        $query = $em->getRepository('BookshopBookshopBundle:Product')->getProducts($id, $request);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $this->get('request')->query->get('page', 1)/* page number */, 9/* limit per page */
        );

        return $this->render('BookshopBookshopBundle:Default:category.html.twig', array(
                    'products' => $pagination,
                    'category' => $category
        ));
    }

}
