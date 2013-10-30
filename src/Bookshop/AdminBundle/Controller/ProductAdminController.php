<?php

namespace Bookshop\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Bookshop\AdminBundle\Form\Type\ProductAddFormType;
use Bookshop\BookshopBundle\Entity\Product;
use Bookshop\BookshopBundle\Entity\Image;

/**
 * Description of UserAdminController
 *
 * @author mzaharie
 */
class ProductAdminController extends Controller {

    public function indexAction(Request $request) 
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('BookshopBookshopBundle:Category')->findAll();
        $query = $em->getRepository('BookshopBookshopBundle:Product')->getAllProductsQuery($request);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $this->get('request')->query->get('page', 1)/* page number */, 
                10/* limit per page */, 
                array('distinct' => false)
                );
        return $this->render('BookshopAdminBundle:ProductAdmin:index.html.twig', array('products' => $pagination, 'categories' => $categories));
    }

    public function addAction() 
    {
        $em = $this->getDoctrine()->getManager();
        $product = new Product();
        $form = $this->createForm(new ProductAddFormType(), $product, array('validation_groups' => array('Add')));
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
        }
        if ($form->isValid()) {
//            $product_service = $this->get("bookshop_bookshop.product.service");
//            $new_product = $product_service->insert($product);
//            var_dump($new_product);
//            die();
            $product->setActive(1);

            $image = new Image();
            $image->setPath("bundles/bookshopbookshop/public/image/");
            if ($product->getFile()) {
                $filename = sha1(uniqid(mt_rand(), true));
                $image->setFilename($filename.".".$product->getFile()->guessExtension());
            } else {
                $image->setFilename('defalut.jpg');
            }
            $em->persist($product);
            $em->flush($product);

            $image->setProductid($product->getId());

            $em->persist($image);
            $em->flush($image);
            $product->setImage($image);

            $product->upload();

            $em->persist($product);
            $em->flush();

            return $this->redirect($this->generateUrl("bookshop_admin_product_list"));
        }
        return $this->render('BookshopAdminBundle:ProductAdmin:add.html.twig', array('form' => $form->createView()));
    }
    
    public function editAction($id) 
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('BookshopBookshopBundle:Product')->find($id);
        if(!$product){
            throw $this->createNotFoundException('Unable to find this product.');
        }
        
        $form = $this->createForm(new ProductAddFormType(), $product, array('validation_groups' => array('Add')));
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
        }
        if ($form->isValid()) {
            $product->setActive(1);

            $image = $product->getImage();
            if(!$image){
                $image = new Image();
                $image->setPath("bundles/bookshopbookshop/public/image/");
            }
            
            if ($product->getFile()) {
                $filename = sha1(uniqid(mt_rand(), true));
                $image->setFilename($filename.".".$product->getFile()->guessExtension());
            }
            
            $em->persist($product);
            $em->flush($product);

            $image->setProductid($product->getId());

            $em->persist($image);
            $em->flush($image);
            $product->setImage($image);
            
            if ($product->getFile()) {
                $product->upload();
            }
            
            $em->persist($product);
            $em->flush();

            return $this->redirect($this->generateUrl("bookshop_admin_product_list"));
        }
        return $this->render('BookshopAdminBundle:ProductAdmin:edit.html.twig', array('form' => $form->createView(), 'id' => $id));
    }
    
    public function deleteAction($id) 
    {
        $em = $this->getDoctrine()->getManager();
        $product = new Product();
        $product = $em->getRepository('BookshopBookshopBundle:Product')->find($id);
        if(!$product){
            throw $this->createNotFoundException('Unable to find this product.');
        }
        
        $product->setActive(0);
        $em->persist($product);
        $em->flush($product);
        
        $url = $this->getRequest()->headers->get("referer");
        return new RedirectResponse($url);
    }
    
    public function undeleteAction($id) 
    {
        $em = $this->getDoctrine()->getManager();
        $product = new Product();
        $product = $em->getRepository('BookshopBookshopBundle:Product')->find($id);
        if(!$product){
            throw $this->createNotFoundException('Unable to find this product.');
        }
        
        $product->setActive(1);
        $em->persist($product);
        $em->flush($product);
        
        $url = $this->getRequest()->headers->get("referer");
        return new RedirectResponse($url);
    }

}