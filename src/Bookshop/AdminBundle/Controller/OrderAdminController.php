<?php

namespace Bookshop\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
/**
 * Description of OrderAdminController
 *
 * @author mzaharie
 */
class OrderAdminController extends Controller{
    
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->getAllOrdersQuery($request);
        
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $this->get('request')->query->get('page', 1)/* page number */, 
                2/* limit per page */, 
                array('distinct' => false)
                );
        
        $states = $em->getRepository("BookshopBookshopBundle:State")->findAll();
        
        return $this->render('BookshopAdminBundle:OrderAdmin:index.html.twig', array('orders' => $pagination, 'states' => $states));
    }
    
    public function setStateAction($id,$stateId){
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository("BookshopBookshopBundle:BookshopOrder")->find($id);
        $state = $em->getRepository("BookshopBookshopBundle:State")->find($stateId);
        $order->setState($state);
        if($state->getId() == 4){
            //$em->getRepository("BookshopBookshopBundle:BookshopOrder")->cancelCart(); // pun produsele din nou pe stoc dar las cartul(si cart_items) inactiv. Astfel raman informatiile despre comanda
            //to do
        }
        
        $em->persist($order);
        $em->flush($order);
        
        $url = $this->getRequest()->headers->get("referer");
        return new RedirectResponse($url);
    }
    
    public function viewAction($id){
        $em = $this->getDoctrine()->getManager();
        
        $order = $em->getRepository("BookshopBookshopBundle:BookshopOrder")->find($id);
        $cartitems = null;
        if($order->getCart()){
        $cartitems = $em->getRepository('BookshopBookshopBundle:CartItems')->getItems($order->getCart()->getId());
        }
        $states = $em->getRepository("BookshopBookshopBundle:State")->findAll();
        
        return $this->render('BookshopAdminBundle:OrderAdmin:view.html.twig', array('order' => $order, 'cartitems' =>$cartitems, 'states' => $states));
    }
    
}

?>
