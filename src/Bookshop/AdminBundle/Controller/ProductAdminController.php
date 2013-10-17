<?php

namespace Bookshop\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Bookshop\AdminBundle\Form\Type\ProductAddFormType;
use Bookshop\BookshopBundle\Entity\Product;
use Bookshop\BookshopBundle\Entity\Image;

/**
 * Description of UserAdminController
 *
 * @author mzaharie
 */
class ProductAdminController extends Controller {

    public function indexAction() {
        $filter = $this->createSqlFilter();
        
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('BookshopBookshopBundle:Category')->findAll();
        $count = $em
                ->createQuery('SELECT COUNT(p) FROM BookshopBookshopBundle:Product p INNER JOIN BookshopBookshopBundle:Category c WITH c = p.category WHERE 1=1' . $filter)
                ->getSingleScalarResult();
        $dql = "SELECT p FROM BookshopBookshopBundle:Product p INNER JOIN BookshopBookshopBundle:Category c WITH c = p.category WHERE 1=1";
        $dql.=$filter;

        $query = $em->createQuery($dql)->setHint('knp_paginator.count', $count);


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $this->get('request')->query->get('page', 1)/* page number */, 
                10/* limit per page */, 
                array('distinct' => false)
                );
        return $this->render('BookshopAdminBundle:ProductAdmin:index.html.twig', array('products' => $pagination, 'categories' => $categories));
    }

    public function addAction() {
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
    
    public function editAction($id) {
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
    
    public function deleteAction($id) {
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
    
    public function undeleteAction($id) {
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
    
    private function createSqlFilter(){
        $filter = "";
        if (isset($_GET['title'])) {
            $filter.= " AND p.title like '%" . $_GET['title'] . "%'";
        }
        if (isset($_GET['category']) && strlen($_GET['category'])>0) {
            $filter.= " AND c.id = " . $_GET['category'];
        }

        if (isset($_GET['stock']))
            switch ($_GET['stock']) {
                case 'on':
                    $filter .= ' and p.stock>0';
                    break;
            } else {
            if (isset($_GET['title']) || isset($_GET['category'])) {
                $filter .= ' and p.stock>=0';
            }
        }
        
        return $filter;
    }

}