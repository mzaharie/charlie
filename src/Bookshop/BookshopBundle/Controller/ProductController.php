<?php

namespace Bookshop\BookshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductController extends Controller {

    public function productAction($id) {

        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('BookshopBookshopBundle:Product')->retrieveProduct($id);
        $categoryid = $product->getCategory()->getId();
        $allproducts = $em->getRepository('BookshopBookshopBundle:Product')->relatedProducts($categoryid);
        
        $nrRandProds = count($allproducts);
        if($nrRandProds > 4) $nrRandProds = 4;
        
        $array = array_rand($allproducts, $nrRandProds); 

        for ($i = 0; $i < 4 && $i<  $nrRandProds; $i++) {
            $relatedproducts[$i] = $allproducts[$array[$i]];
        }

        return $this->render('BookshopBookshopBundle:Default:productdetail.html.twig', array(
                    'product' => $product,
                    'relatedproducts' => $relatedproducts
                        )
        );
    }

}
