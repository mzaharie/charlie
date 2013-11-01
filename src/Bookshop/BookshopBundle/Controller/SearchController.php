<?php

namespace Bookshop\BookshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
    
    public function searchHintsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $hints = $em->getRepository('BookshopBookshopBundle:Product')->searchHints($request);
        $keys = array();
        foreach ($hints as $hint){
            $words = explode(" ", $hint->getTitle());
            foreach ($words as $word){
                if(!in_array($word, $keys) && strstr($word,$request->get('q'))){
                    array_push($keys, $word);
                }
            }
        }
        $response = '';
        if(!$hints){
            $response = '';
        }
        else{
            $response .= "<ul>";
            $response .= "<li></li>";
            foreach ($keys as $key){
                    $response .= "<li>".$key."</li>";
            }
            $response .= "</ul>";
        }
        return new Response($response);
    }

}

