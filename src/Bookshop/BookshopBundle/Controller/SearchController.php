<?php

namespace Bookshop\BookshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller {

    public function searchAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $categs = $em->getRepository('BookshopBookshopBundle:Category')->findAll();
        $query = $em->getRepository('BookshopBookshopBundle:Product')->searchProduct($request);
        $paginator = $this->get('knp_paginator');
        $products = $paginator->paginate(
                $query, $this->get('request')->query->get('page', 1)/* page number */, 9/* limit per page */
        );

        return $this->render('BookshopBookshopBundle:Default:search.html.twig', array(
                    'products' => $products,
                    'string' => $request->get('q'),
                    'categories' => $categs
        ));
    }

}

