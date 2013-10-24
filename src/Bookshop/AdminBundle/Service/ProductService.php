<?php

namespace Bookshop\AdminBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.1.0+
use Bookshop\BookshopBundle\Entity\Product;
use Bookshop\BookshopBundle\Entity\Image;
/*
 * for persistence only
 */
class ProductService
{
    private $em;
    
    public function __construct(Doctrine $doctrine)
    {
        $this->em = $doctrine->getManager();
    }
    
    public function persistEdited(Product $product)
    {
        $em = $this->em;
        $product->setActive(1);

        $image = $product->getImage();
        if(!$image){
            $image = new Image();
            $image->setPath("bundles/bookshopbookshop/public/image/");
        }

        if ($product->getFile()) {
            $filename = sha1(uniqid(mt_rand(), true));
            $image->setFilename($filename.".".$product->getFile()->guessExtension());
        }

        $em->persist($product);
        $em->flush($product);

        $image->setProductid($product->getId());

        $em->persist($image);
        $em->flush($image);
        $product->setImage($image);

        if ($product->getFile()) {
            $product->upload();
        }

        $em->persist($product);
        $em->flush();
    }
    
    public function persistNew(Product $product)
    {
        $product->setActive(1);

        $image = new Image();
        $image->setPath("bundles/bookshopbookshop/public/image/");
        if ($product->getFile()) {
            $filename = sha1(uniqid(mt_rand(), true));
            $image->setFilename($filename.".".$product->getFile()->guessExtension());
        } else {
            $image->setFilename('defalut.jpg');
        }
        $em->persist($product);
        $em->flush($product);

        $image->setProductid($product->getId());

        $em->persist($image);
        $em->flush($image);
        $product->setImage($image);

        $product->upload();

        $em->persist($product);
        $em->flush();

    }
}