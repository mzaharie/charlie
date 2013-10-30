<?php

namespace Bookshop\BookshopBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware {

    public function linksMenu(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root');
        $myTrans = $this->container->get('translator');

        $securityContext = $this->container->get('security.context');

        $menu->addChild('Home', array(
            'route' => 'bookshop_bookshop_homepage'
        ));



        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            $myAccountTrans = $myTrans->trans('menu.my.account', array(), 'BookshopBundle');
            $menu->addChild($myAccountTrans, array(
                'route' => 'dashboard_index'
            ));
            $menu->addChild('Checkout', array(
                'route' => 'checkout'
            ));

            $myCartTrans = $myTrans->trans('menu.my.cart', array(), 'BookshopBundle');
            $menu->addChild($myCartTrans, array(
                'route' => 'mycart'
            ));

            $logoutTrans = $myTrans->trans('layout.logout', array(), 'FOSUserBundle');
            $menu->addChild($logoutTrans, array('route' => 'fos_user_security_logout'));
        } else {

            $loginTrans = $myTrans->trans('layout.login', array(), 'FOSUserBundle');
            $menu->addChild($loginTrans, array('route' => 'fos_user_security_login'));

            $rstPassTrans = $myTrans->trans('menu.forgot_pass', array(), 'BookshopBundle');
            $menu->addChild($rstPassTrans, array('route' => 'fos_user_resetting_request'));

            $registerTrans = $myTrans->trans('menu.register', array(), 'BookshopBundle');
            $menu->addChild($registerTrans, array('route' => 'fos_user_registration_register'));
//            $menu->addChild("Item With Childs", array('route' => '_welcome'));
//            $menu['Item With Childs']->addChild('first Cild', array('route' => '_welcome'));
//            $menu['Item With Childs']->addChild('second Cild', array('route' => '_welcome'));
            $menu->addChild('Checkout', array(
                'route' => 'checkout'
            ));

            $myCartTrans = $myTrans->trans('menu.my.cart', array(), 'BookshopBundle');
            $menu->addChild($myCartTrans, array(
                'route' => 'mycart'
            ));

//            $rstPassTrans = $myTrans->trans('menu.forgot_pass', array(), 'BookshopBundle');
//            $menu->addChild($rstPassTrans, array('route' => 'fos_user_resetting_request'));
//            $registerTrans = $myTrans->trans('menu.register', array(), 'BookshopBundle');
//            $menu->addChild($registerTrans, array('route' => 'fos_user_registration_register'));
        }
        return $menu;
    }

    public function mainMenu(FactoryInterface $factory, array $options) {
        $em = $this->container->get('doctrine.orm.entity_manager');
//        var_dump($em->getRepository('Gedmo\Translatable\Entity\Translation')->findTranslations($cat));
        $categs = $em->getRepository('BookshopBookshopBundle:Category')->findAll();

        $menu = $factory->createItem('root');
        foreach ($categs as $categ) {
//            $categ->setTranslatableLocale('ro');    
//            $em->refresh($categ);
//            var_dump($categ->getLocale());
//            $categ->setTranslatableLocale('ro');
            $menu->addChild($categ->getName(), array(
                'route' => 'category',
                'routeParameters' => array('id' => $categ->getID())));
        }

        return $menu;
    }

    public function dashboardMenu(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root');
        $myTrans = $this->container->get('translator');

        $dashHomeTrans = $myTrans->trans('menu.dashboard.home', array(), 'BookshopBundle');
        $menu->addChild($dashHomeTrans, array(
            'route' => 'dashboard_index'
        ));
        $accInfoTrans = $myTrans->trans('menu.dashboard.account_info', array(), 'BookshopBundle');
        $menu->addChild($accInfoTrans, array(
            'route' => 'fos_user_profile_show'
        ));
        $myOrdersTrans = $myTrans->trans('menu.dashboard.account_orders', array(), 'BookshopBundle');
        $menu->addChild($myOrdersTrans, array(
            'route' => 'dashboard_orders'  // not existing feature
        ));
        $billingTrans = $myTrans->trans('menu.dashboard.address.billing', array(), 'BookshopBundle');
        $menu->addChild($billingTrans, array(
            'route' => 'dashboard_billing_addr_preedit'  // not existing feature
        ));
        $shippingTrans = $myTrans->trans('menu.dashboard.address.shipping', array(), 'BookshopBundle');
        $menu->addChild($shippingTrans, array(
            'route' => 'dashboard_shipping_addr_preedit'  // not existing feature
        ));
        return $menu;
    }

}
