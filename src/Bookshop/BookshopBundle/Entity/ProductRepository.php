<?php

namespace Bookshop\BookshopBundle\Entity;

use Doctrine\ORM\EntityRepository;


/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends EntityRepository {

    public function getProducts($categoryid) {
        $qb = $this->createQueryBuilder('c')
                ->select('c')->where('c.category = :categ_id AND c.active = 1')
                ->setParameter('categ_id', $categoryid);

        return $qb->getQuery()
                        ->getResult();
    }

    public function getLast($nr) {
        $qb = $this->createQueryBuilder('l')
                ->select('l')->orderBy('l.id', 'desc')
                ->setMaxResults($nr);

        return $qb->getQuery()
                        ->getResult();
    }

    public function retrieveProduct($productid) {

        $qb = $this->createQueryBuilder('p')
                ->select('p')->where('p.id = :product_id')
                ->setParameter('product_id', $productid);

        return $qb->getQuery()
                        ->getResult();
    }

    public function relatedProducts($categoryid) {

        $qb = $this->createQueryBuilder('r')
                ->select('r')->where('r.category = :categoryid and r.active = 1')
                ->setParameter('categoryid', $categoryid);

        return $qb->getQuery()
                        ->getResult();
    }

}
