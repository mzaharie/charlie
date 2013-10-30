<?php

namespace Bookshop\BookshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller 
{

    public function mycartAction() {
        if (is_null($this->getUser()))
            $userid = 0;
        else
            $userid = $this->getUser()->getID();
        $em = $this->getDoctrine()->getManager();
        $cart = $em->getRepository('BookshopBookshopBundle:Cart')->getCart($userid);
        $cartid = $cart->getId();
        $cartitems = $em->getRepository('BookshopBookshopBundle:CartItems')->getItems($cartid);

        return $this->render('BookshopBookshopBundle:Default:mycart.html.twig', array(
                    'items' => $cartitems,
                    'cart' => $cart
                        )
        );
    }

    public function updatecartAction(Request $request) {
        if ($this->match($request->request->get('cartid')) == 1) {
            $em = $this->getDoctrine()->getManager();
            $cartitems = $em->getRepository('BookshopBookshopBundle:CartItems')->getItems($request->request->get('cartid'));
            $success = 1;
            if (is_array($request->request->get('qty'))) {
                foreach ($request->request->get('qty') as $key => $value)
                    foreach ($cartitems as $cartitem)
                        if ($cartitem->getID() == $key)
                            if ($value <= $cartitems[0]->getProductID()->getStock()) {
                                if ($value > 0) {
                                    $cartitem->setQuantity($value);
                                    $em->persist($cartitem);
                                } else {
                                    $this->deleteproductAction($cartitem->getID(), $request->request->get('cartid'));
                                }
                            } else {
                                $success = 0;
                            }
                $em->flush();
            }
            if ($success == 0)
                $this->getRequest()->getSession()->getFlashBag()->add('error', 'Some values were not updated');
            else
                $this->getRequest()->getSession()->getFlashBag()->add('success', 'Cart has been updated');
            $this->updateTotalCart($request->request->get('cartid'));
            $referer = $this->getRequest()->headers->get('referer');
            return $this->redirect($referer);
        } else {
            return $this->render('BookshopBookshopBundle:Error:Error.html.twig');
        }
    }

    public function deleteproductAction($id, $cartid) {
        if ($this->match($cartid) == 1) {
            $em = $this->getDoctrine()->getManager();
            $cart = $em->getRepository('BookshopBookshopBundle:Cart')->getCartbyId($cartid);
            $cartitems = $em->getRepository('BookshopBookshopBundle:CartItems')->getItems($cartid);
            if (!(empty($cartitems))) {
                foreach ($cartitems as $cartitem) {
                    if ($cartitem->getId() == $id) {
                        $em->remove($cartitem);
                        $em->flush();
                    }
                }
            }
            $this->updateTotalCart($cartid);
            $this->getRequest()->getSession()->getFlashBag()->add('success', 'Product deleted');
            $referer = $this->getRequest()->headers->get('referer');
            return $this->redirect($referer);
        } else {
            return $this->render('BookshopBookshopBundle:Error:Error.html.twig');
        }
    }

    public function emptycartAction($cartid) {
        if ($this->match($cartid) == 1) {
            $em = $this->getDoctrine()->getManager();
            $cart = $em->getRepository('BookshopBookshopBundle:Cart')->getCartbyId($cartid);
            $cartitems = $em->getRepository('BookshopBookshopBundle:CartItems')->getItems($cartid);
            if (!(empty($cartitems))) {
                foreach ($cartitems as $cartitem) {
                    $em->remove($cartitem);
                    $em->flush();
                }
            }
            $this->updateTotalCart($cartid);
            $this->getRequest()->getSession()->getFlashBag()->add('success', 'Cart is now empty');
            $referer = $this->getRequest()->headers->get('referer');

            return $this->redirect($referer);
        } else {
            return $this->render('BookshopBookshopBundle:Error:Error.html.twig');
        }
    }

    public function addproductAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        if (strlen($request->request->get('productid'))>0)
            if (ctype_digit($request->request->get('productid')))
                $productid = $request->request->get('productid');
        if (strlen($request->request->get('quantity'))>0)
            if (ctype_digit($request->request->get('quantity')))
                $quantity = $request->request->get('quantity');
        if (is_null($this->getUser()))
            $userid = 0;
        else
            $userid = $this->getUser()->getID();
        $em = $this->getDoctrine()->getManager();
        $cart = $em->getRepository('BookshopBookshopBundle:Cart')->getCart($userid);
        $product = $em->getRepository('BookshopBookshopBundle:Product')->retrieveProduct($productid);
        $existitem = $em->getRepository('BookshopBookshopBundle:CartItems')->getCartItem($productid, $cart->getId());
        $success = 1;
        if (empty($existitem[0])) {
            if ($quantity <= $product->getStock()) {
                $cartitem = new \Bookshop\BookshopBundle\Entity\CartItems;
                $cartitem->setPrice($product->getPrice());
                $cartitem->setTitle($product->getTitle());
                $cartitem->setQuantity($quantity);
                $cartitem->setProductId($product);

                $cartitem->setCartId($cart);
                $em->persist($cartitem);
                $em->flush();
            } else {
                $success = 0;
            }
        } else {
            if ($quantity + $existitem[0]->getQuantity() <= $product->getStock()) {
                $existitem[0]->setQuantity($existitem[0]->getQuantity() + $quantity);
                $em->persist($cart);
                $em->persist($existitem[0]);
                $em->flush();
            } else {
                $success = 0;
            }
        }
        if ($success == 0)
            $this->getRequest()->getSession()->getFlashBag()->add('error', "We don't have the quantity you requested");
        else
            $this->getRequest()->getSession()->getFlashBag()->add('success', 'Product was successfully added');
        $this->updateTotalCart($cart->getId());
        $this->updateTotalCart($cart->getId());
        $referer = $this->getRequest()->headers->get('referer');

        return $this->redirect($referer);
    }

    public function indexAction() {
        if (is_null($this->getUser()))
            $userid = 0;
        else
            $userid = $this->getUser()->getID();

        $em = $this->getDoctrine()->getManager();

        $cart = $em->getRepository('BookshopBookshopBundle:Cart')->getCart($userid);

        if ($cart == null) {  //$cart is an object of type Cart or a null
            $cartmodel = new \Bookshop\BookshopBundle\Entity\Cart;
            $cartmodel->setUserId($userid);
            $cartmodel->setDate(date('Y-m-d'));
            $cartmodel->setTotal(0);
            $cartmodel->setActive(1);
            $em->persist($cartmodel);
            $em->flush();
            $cart = $em->getRepository('BookshopBookshopBundle:Cart')->getCart($userid);
        }
        
        $cartid = $cart->getId();

        $cartitems = $em->getRepository('BookshopBookshopBundle:CartItems')->getItems($cartid);

        return $this->render('BookshopBookshopBundle:Cart:index.html.twig', array(
                    'items' => $cartitems,
                    'cart' => $cart
                        )
        );
    }

    private function updateTotalCart($cartid) {
        if ($this->match($cartid) == 1) {
            if (is_null($this->getUser()))
                $userid = 0;
            else
                $userid = $this->getUser()->getID();
            $em = $this->getDoctrine()->getManager();
            $cart = $em->getRepository('BookshopBookshopBundle:Cart')->getCart($userid);
            $cartitems = $em->getRepository('BookshopBookshopBundle:CartItems')->getItems($cartid);

            $cart->setTotal(0.00);

            foreach ($cartitems as $item) {
                $cart->setTotal($item->getQuantity() * $item->getPrice() + $cart->getTotal());
            }
            $em->persist($cart);
            $em->flush();
        } else {
            return $this->render('BookshopBookshopBundle:Error:Error.html.twig');
        }
    }

    private function match($cartid) {
        if (is_null($this->getUser()))
            $userid = 0;
        else
            $userid = $this->getUser()->getID();
        $em = $this->getDoctrine()->getManager();
        $cart = $em->getRepository('BookshopBookshopBundle:Cart')->getCartbyId($cartid);
        if ($userid != $cart->getUserID()) {
            return 0;
        } else {
            return 1;
        }
    }

}
