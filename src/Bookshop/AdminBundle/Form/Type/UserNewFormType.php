<?php

// src/Bookshop/BookshopBundle/Form/Type/ProfileFormType.php

namespace Bookshop\AdminBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class UserNewFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        // add your custom field
        $builder->add('username');
        $builder->add('first_name');
        $builder->add('last_name');
        $builder->add('email');
        $builder->add('mobile');
        $builder->add('plainPassword', "hidden" ,array(
            'data' => '000000',
        ));
    }

    public function getName() {
        return 'admin_user_new';
    }

}