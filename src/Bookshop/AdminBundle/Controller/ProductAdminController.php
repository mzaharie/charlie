<?php

namespace Bookshop\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Bookshop\AdminBundle\Form\Type\ProductAddFormType;
use Bookshop\BookshopBundle\Entity\Product;

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
            $productService = $this->get('product_service');
            $productService->persistNew($product);

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
            $productService = $this->get('product_service');
            $productService->persistEdited($product);

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