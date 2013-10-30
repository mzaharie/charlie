<?php

namespace Bookshop\BookshopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Description of CheckoutBillingFormType
 *
 * @author mzaharie
 */
class CheckoutBillingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName','text', array('required' => false))
            ->add('lastName','text', array('required' => false))
            ->add('email','text', array('required' => false))
            ->add('address','text', array('required' => false))
            ->add('city','text', array('required' => false))
            ->add('country','text', array('required' => false))
            ->add('shipp_to_this',
                  'choice', 
                  array(
                    'choices' => array(true => 'Shipp to this address', false => 'Shipp to different address'),
                    'required' => true,
                    'expanded' => true,
                    'multiple' => false,

                    'data' => true
                        )
                  )
        ;
    }

    public function getName()
    {
        return 'default_address_type';
    }
}


