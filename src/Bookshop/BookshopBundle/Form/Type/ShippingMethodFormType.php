<?php
// src/Blogger/BlogBundle/Form/CommentType.php

namespace Bookshop\BookshopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ShippingMethodFormType extends AbstractType
{
    private $em;
    
    public function __construct($em) {
        $this->em = $em;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('shipping',
                    'entity',
                    array(
                        'class' => 'BookshopBookshopBundle:ShippingMethod',
                        'required' => true,
                        'expanded' => true,
                        'multiple' => false,
                        'data' => $this->em->getReference("BookshopBookshopBundle:Shippingmethod", 1)
                        )
                    )
        ;
    }

    public function getName()
    {
        return 'shipping_method_type';
    }
}