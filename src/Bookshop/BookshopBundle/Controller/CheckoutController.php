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

class CheckoutController extends Controller
{

    public function indexAction()
    {
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
            if (!$this->getUser()) {
                $this->getRequest()->getSession()->getFlashBag()->add('error', "Please login before checkout!");
                $url = $this->getRequest()->headers->get("referer");
                return new RedirectResponse($url);
            }
            $step = $this->dispatchToStep();
            return $this->redirect($this->generateUrl($step));
        }
    }

    public function billingAction()
    {
        if (!$this->getUser()) {
            $this->setFlashMessage('error', 'checkout.error.please_login');
            $url = $this->getRequest()->headers->get("referer");
            if(strlen($url)==0){
                return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
            }else{
                return new RedirectResponse($url);
            }
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
            $this->setFlashMessage('error', 'checkout.error.please_login');
            $url = $this->getRequest()->headers->get("referer");
            if(strlen($url)==0){
                return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
            }else{
                return new RedirectResponse($url);
            }
        }
        $step = $this->dispatchToStep();
        if($step != false && $step != 'shipping' && $step != 'shipping_method' && $step != 'payment' && $step != 'review'){
            return $this->redirect($this->generateUrl($step));
        }
        
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->getCurrentOrder($this->getUser()->getID());
        $address = new Address();
        if ($user->getShippingAddress())
            $address = $user->getShippingAddress();
        
        $form = $this->createForm(new AddressFormType(), $address);
        $request = $this->getRequest();
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

    public function shippingMethodAction(Request $request)
    {
        if (!$this->getUser()) {
            $this->setFlashMessage('error', 'checkout.error.please_login');
            $url = $this->getRequest()->headers->get("referer");
            if(strlen($url)==0){
                return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
            }else{
                return new RedirectResponse($url);
            }
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

    public function paymentAction()
    {
       if (!$this->getUser()) {
            $this->setFlashMessage('error', 'checkout.error.please_login');
            $url = $this->getRequest()->headers->get("referer");
            if(strlen($url)==0){
                return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
            }else{
                return new RedirectResponse($url);
            }
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

    public function reviewAction()
    {
        if (!$this->getUser()) {
            $this->setFlashMessage('error', 'checkout.error.please_login');
            $url = $this->getRequest()->headers->get("referer");
            if(strlen($url)==0){
                return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
            }else{
                return new RedirectResponse($url);
            }
        }
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->getCurrentOrder($this->getUser()->getID());
        
        return $this->render('BookshopBookshopBundle:Checkout:review.html.twig', array('order' => $order));
    }
    
    public function cancelAction()
    {
        if (!$this->getUser()) {
            $this->setFlashMessage('error', 'checkout.error.please_login');
            $url = $this->getRequest()->headers->get("referer");
            if(strlen($url)==0){
                return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
            }else{
                return new RedirectResponse($url);
            }
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
    
    /**
     * user-ul poate renunta(face cancel) doar daca order-ul este in starile 1 si 2, si ii apartine
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function userCancelAction(Request $request)
    {
        if (!$this->getUser()) {
            $this->setFlashMessage('error', 'checkout.error.please_login');
            $url = $this->getRequest()->headers->get("referer");
            if(strlen($url)==0){
                return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
            }else{
                return new RedirectResponse($url);
            }
        }
        $id = null;
        if ($request->getMethod() == 'POST') {
            $id = $request->request->get('id');
        }
        
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $order = new BookshopOrder();
        $order = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->find($id);
        if(!$order){
            $this->setFlashMessage('error', 'checkout.error.order.not_found');
            return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
        }
        $ok = true;
        if($order->getUser()->getId() != $user->getId()){
            $ok = false;
            $this->setFlashMessage('error', 'checkout.error.order.not_your');
            return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
        }
        elseif(!$order->getState()){
            $ok = false;
            $this->setFlashMessage('error', 'checkout.error.order.bad');
            return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
        }
        elseif(in_array($order->getState()->getId(), array(3,5) )){
            $ok = false;
            $this->setFlashMessage('error', 'checkout.error.order.cannot_cancel');
            return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
        }
        elseif($order->getState()->getId() == 4){
            $ok = false;
            $this->setFlashMessage('error', 'checkout.error.order.already_canceled');
            return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
        }
            
        $state = $em->getRepository('BookshopBookshopBundle:State')->find(4);  //failed state
        if($ok == true){
            $order->setState($state);
            $em->persist($order);
            $em->flush($order);
            $this->setFlashMessage('error', 'checkout.success.canceled');
        }
        return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
    }
    
    public function placeOrderAction()
    {
        if (!$this->getUser()) {
            $this->setFlashMessage('error', 'checkout.error.please_login');
            $url = $this->getRequest()->headers->get("referer");
            if(strlen($url)==0){
                return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
            }else{
                return new RedirectResponse($url);
            }
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
            ->setBody($this->renderView('BookshopBookshopBundle:Checkout:orderEmail.html.twig', array('order' => $order)), 'text/html');
        $this->get('mailer')->send($message);
        
        return $this->render('BookshopBookshopBundle:Checkout:success.html.twig', array('order' => $order));
    }
    
    public function orderDetailsAction($id)
    {
        if (!$this->getUser()) {
            $this->setFlashMessage('error', 'checkout.error.please_login');
            $url = $this->getRequest()->headers->get("referer");
            if(strlen($url)==0){
                return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
            }else{
                return new RedirectResponse($url);
            }
        }
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('BookshopBookshopBundle:BookshopOrder')->find($id);
        if(!$order){
            $this->getRequest()->getSession()->getFlashBag()->add('error', "Order not found!");
            return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
        }
        elseif($order->getUser()->getId() != $user->getId()){
            $this->getRequest()->getSession()->getFlashBag()->add('error', "This is not your order!");
            return $this->redirect($this->generateUrl('bookshop_bookshop_homepage'));
        }
        return $this->render('BookshopBookshopBundle:Checkout:orderDetails.html.twig', array('order' => $order));
    }


    private function dispatchToStep()
    {
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
    
    private function setFlashMessage($category, $value)
    {
        $value = $this->container->get('translator')->trans($value, array(), 'BookshopBundle');
        $this->getRequest()->getSession()->getFlashBag()->add($category, $value);
    }

}