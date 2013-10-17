<?php

namespace Bookshop\BookshopBundle\Entity;
use Bookshop\BookshopBundle\Entity\Image;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Description of ProductService
 *
 * @author mzaharie
 */
class ProductService extends ContainerAware{
    
    public function insert($product)
    {
        $product->setActive(1);

        $image = new Image();
        $image->setPath("bundles/bookshopbookshop/public/image/");
        if ($product->getFile()) {
            $filename = sha1(uniqid(mt_rand(), true));
            $image->setFilename($filename . "." . $product->getFile()->guessExtension());
        } else {
            $image->setFilename('defalut.jpg');
        }
        $em = $this->container->get('doctrine');
        $em->persist($product);
        $em->flush($product);

        $image->setProductid($product->getId());

        $em->persist($image);
        $em->flush($image);
        $product->setImage($image);

        $product->upload();

        $em->persist($product);
        $em->flush();
        
        return $product;
    }
}

?>
