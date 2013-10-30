<?php
// src/Blogger/BlogBundle/Form/CommentType.php

namespace Bookshop\BookshopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PaymentMethodFormType extends AbstractType
{
    private $em;
    
    public function __construct($em) {
        $this->em = $em;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $currentPayment = $this->em->getRepository('BookshopBookshopBundle:BookshopOrder')->getCurrentOrder($this->getUser()->getID());
        $builder
            ->add('payment',
                    'entity',
                    array(
                        'class' => 'BookshopBookshopBundle:PaymentMethod',
                        'required' => true,
                        'expanded' => true,
                        'multiple' => false,
                        'data' => $this->em->getReference("BookshopBookshopBundle:PaymentMethod", 1)
                        )
                    )
        ;
    }

    public function getName()
    {
        return 'payment_method_type';
    }
}