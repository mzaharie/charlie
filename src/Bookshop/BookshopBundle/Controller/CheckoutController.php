<?php

namespace Bookshop\BookshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Bookshop\BookshopBundle\Form\Type\CheckoutBillingFormType;
use Bookshop\BookshopBundle\Form\Type\ShippingMethodFormType;
use Bookshop\BookshopBundle\Form\Type\PaymentMethodFormType;
use Bookshop\BookshopBundle\Form\Type\AddressFormType;
use Bookshop\BookshopBundle\Entity\Address;
use Bookshop\BookshopBundle\Entity\BookshopOrder;

class CheckoutController extends Controller {

    public function indexAction() {
        if (is_null($this->getUser()))
            $userid = 0;
        else
            $userid = $this->getUser()->getID();
        $em = $this->getDoctrine()->getManager();
        $cart = $em->getRepository('BookshopBookshopBundle:Cart')->getCart($userid);
        $cartid = $cart->getId();
        $cartitems = $em->getRepository('BookshopBookshopBundle:CartItems')->getItems($cartid);
        if (sizeof($cartitems) == 0) {
            return $this->render("BookshopBookshopBundle:Error:Empty.html.twig");
        } else {
            $step = $this->dispatchToStep();
            return $this->redirect($this->generateUrl($step));
        }
    }

    public function billingAction() {
        if (!$this->getUser()) {
            $this->getRequest()->getSession()->getFlashBag()->add('error', "Please login before checkout!");
            $url = $this->getRequest()->headers->get("referer");
            return new RedirectResponse($url);
        }
        $user = $this->getUser();
        $userid = $this->getUser()->getID();

        $em = $this->getDoctrine()->getManager();
        $state = $em->getRepository('BookshopBookshopBundle:State')->find(1);
        $cart = $em->getRepository('BookshopBookshopBundle:Cart')->getCart($userid);
        $order = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->getCurrentOrder($userid);
        if(!$order){
            $order = new BookshopOrder();
        }
        
        
        $address = new Address();
        if ($user->getBillingAddress()) {
            $address = $user->getBillingAddress();
        }
        $request = $this->getRequest();

        $form = $this->createForm(new CheckoutBillingFormType(), $address);
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
        }

        if ($form->isValid()) {
            $em->persist($address);
            $em->flush($address);
            $order->setBillingAddress($address);
            if ($address->getShippToThis())
                $order->setShippingAddress($address);
            $order->setUser($user);
            $order->setCart($cart);
            $order->setTotal($cart->getTotal());
            $order->setDate(new \DateTime());
            $order->setState($state);
            $em->persist($order);
            $em->flush($order);
            
            if (!$address->getShippToThis())
                return $this->redirect($this->generateUrl('shipping'));
            else
                return $this->redirect($this->generateUrl('shipping_method'));
        }

        return $this->render('BookshopBookshopBundle:Checkout:billing.html.twig', array('form' => $form->createView()));
    }

    public function shippingAction() 
    {
        if (!$this->getUser()) {
            $this->getRequest()->getSession()->getFlashBag()->add('error', "Please login before checkout!");
            $url = $this->getRequest()->headers->get("referer");
            return new RedirectResponse($url);
        }
        $step = $this->dispatchToStep();
        if($step != false && $step != 'shipping' && $step != 'shipping_method' && $step != 'payment' && $step != 'review'){
            return $this->redirect($this->generateUrl($step));
        }
        
        $user = $this->getUser();
        $userid = $this->getUser()->getID();

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->getCurrentOrder($userid);
        $address = new Address();
        if ($user->getShippingAddress()) {
            $address = $user->getShippingAddress();
        }
        $request = $this->getRequest();

        $form = $this->createForm(new AddressFormType(), $address);
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
        }
        
        if($form->isValid()){
            $em->persist($address);
            $em->flush($address);
            $order->setShippingAddress($address);
            $em->persist($order);
            $em->flush($order);
            return $this->redirect($this->generateUrl('shipping_method'));
        }
        
        return $this->render('BookshopBookshopBundle:Checkout:shipping.html.twig', array('form' => $form->createView()));
    }

    public function shippingMethodAction(Request $request) {
        if (!$this->getUser()) {
            $this->getRequest()->getSession()->getFlashBag()->add('error', "Please login before checkout!");
            $url = $this->getRequest()->headers->get("referer");
            return new RedirectResponse($url);
        }
        $step = $this->dispatchToStep();
        if($step != false && $step != 'shipping_method' && $step != 'payment' && $step != 'review'){
            return $this->redirect($this->generateUrl($step));
        }
        
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $order = new BookshopOrder();
        $order = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->getCurrentOrder($this->getUser()->getID());

        $form = $this->createForm(new ShippingMethodFormType($em), $order);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
        }
        
        if($form->isValid()){
            $order->setTotal($order->getCart()->getTotal()+$order->getShipping()->getPrice());
            $em->persist($order);
            $em->flush($order);
            return $this->redirect($this->generateUrl('payment'));
        }
        
        $back_route = '';
        if($order->getBillingAddress()->getId() == $order->getShippingAddress()->getId())
            $back_route = 'billing';
        else
            $back_route = 'shipping';
        
        return $this->render('BookshopBookshopBundle:Checkout:methodshipping.html.twig', array(
            'form' => $form->createView(),
            'back_route' => $back_route
                ));
    }

    public function paymentAction() {
        if (!$this->getUser()) {
            $this->getRequest()->getSession()->getFlashBag()->add('error', "Please login before checkout!");
            $url = $this->getRequest()->headers->get("referer");
            return new RedirectResponse($url);
        }
        $step = $this->dispatchToStep();
        if($step != false && $step != 'payment' && $step != 'review'){
            return $this->redirect($this->generateUrl($step));
        }
        
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->getCurrentOrder($this->getUser()->getID());
        
        $form = $this->createForm(new PaymentMethodFormType($em), $order);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
        }
        if($form->isValid()){
            $em->persist($order);
            $em->flush($order);
            return $this->redirect($this->generateUrl('review'));
        }
        return $this->render('BookshopBookshopBundle:Checkout:payment.html.twig', array(
            'form' => $form->createView(),
                ));
    }

    public function reviewAction() {
        if (!$this->getUser()) {
            $this->getRequest()->getSession()->getFlashBag()->add('error', "Please login before checkout!");
            $url = $this->getRequest()->headers->get("referer");
            return new RedirectResponse($url);
        }
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->getCurrentOrder($this->getUser()->getID());
        
        return $this->render('BookshopBookshopBundle:Checkout:review.html.twig', array('order' => $order));
    }
    
    public function cancelAction(){
        if (!$this->getUser()) {
            $this->getRequest()->getSession()->getFlashBag()->add('error', "Please login before checkout!");
            $url = $this->getRequest()->headers->get("referer");
            return new RedirectResponse($url);
        }
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $order = new BookshopOrder();
        $order = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->getCurrentOrder($this->getUser()->getID());
        $order->setDate(null);
        $order->setPayment();
        $order->setShipping();
        $order->setShippingAddress();
        $order->setBillingAddress();
        $order->setState(null);
        $order->setTotal(0);
        $em->persist($order);
        $em->flush($order);
        
        return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
    }
    
    public function placeOrderAction()
    {
        if (!$this->getUser()) {
               $this->getRequest()->getSession()->getFlashBag()->add('error', "Please login before checkout!");
               $url = $this->getRequest()->headers->get("referer");
               return new RedirectResponse($url);
           }
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $order = new BookshopOrder();
        $order = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->getCurrentOrder($this->getUser()->getID());
        $state = $em->getRepository('BookshopBookshopBundle:State')->find(2);
        
        $cart = $order->getCart();
        $cart->setActive(0);
        $em->persist($cart);
        $em->flush($cart);
        
        $order->setState($state);
        $em->persist($order);
        $em->flush($order);
        
        $message = \Swift_Message::newInstance()
            ->setSubject('Contact enquiry from symblog')
            ->setFrom('office@bookshop.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('BookshopBookshopBundle:Checkout:orderEmail.html.twig', array('order' => $order)));
        $this->get('mailer')->send($message);
        
        return $this->render('BookshopBookshopBundle:Checkout:success.html.twig', array('order' => $order));
    }
    
    public function orderDetailsAction($id)
    {
        if (!$this->getUser()) {
            $this->getRequest()->getSession()->getFlashBag()->add('error', "Please login before checkout!");
            $url = $this->getRequest()->headers->get("referer");
            return new RedirectResponse($url);
        }
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->find($id);
        
        return $this->render('BookshopBookshopBundle:Checkout:orderDetails.html.twig', array('order' => $order));
    }


    private function dispatchToStep(){
        $user = $this->getUser();
        $userid = $user->getID();

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->getCurrentOrder($userid);
        
        if(!$order)
            return 'billing';
        elseif(!$order->getBillingAddress())
            return 'billing';
        elseif(!$order->getShippingAddress())
            return 'shipping';
        elseif(!$order->getShipping())
            return 'shipping_method';
        elseif(!$order->getPayment())
            return 'payment';
        else
            return 'review';
        
    }

}