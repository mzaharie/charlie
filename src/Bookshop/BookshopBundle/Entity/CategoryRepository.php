<?php

namespace Bookshop\BookshopBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository
{
    public function getCategory($id){
        $qb = $this->createQueryBuilder('c')
                ->select('c')->where('c.id = :categ_id')
                ->setParameter('categ_id', $id);
        
        return $qb->getQuery()->getResult();
    }
            
    
}