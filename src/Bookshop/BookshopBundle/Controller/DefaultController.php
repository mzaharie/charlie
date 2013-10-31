<?php

namespace Bookshop\BookshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        if($this->getUser() && in_array('ROLE_SUPER_ADMIN', $this->getUser()->getRoles())  ){
            return $this->redirect($this->generateUrl('bookshop_admin_homepage'));
        }
        $lastprod = $em->getRepository('BookshopBookshopBundle:Product')->getLast(6);
         if (!$lastprod) {
            throw $this->createNotFoundException('Unable to find last products.');
        }        
        
        return $this->render(
            'BookshopBookshopBundle:Default:homepage.html.twig',
            array(
                'lastProducts' => $lastprod
            )
        );
    }
    
    public function messagesAction()
    {
        return $this->render('BookshopBookshopBundle:Default:messages.html.twig');
    }
}
