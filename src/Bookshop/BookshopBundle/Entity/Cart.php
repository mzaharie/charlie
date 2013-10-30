<?php

namespace Bookshop\BookshopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 *
 * @ORM\Table(name = "cart")
 * @ORM\Entity(repositoryClass="Bookshop\BookshopBundle\Entity\CartRepository")
 */
class Cart {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="date", type="string",length=255)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="integer")
     */
    private $active;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="decimal", precision=10, scale=2)
     */
    private $total;

    /**
     * @ORM\OneToMany(targetEntity="CartItems", mappedBy="cartId")
     */
    private $cartitems;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Cart
     */
    public function setUserId($userId) {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Cart
     */
    public function setDate($date) {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Set active
     *
     * @param integer $active
     * @return Cart
     */
    public function setActive($active) {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer 
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * Set total
     *
     * @param float $total
     * @return Cart
     */
    public function setTotal($total) {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float 
     */
    public function getTotal() {
        return $this->total;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cartitems = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add cartitems
     *
     * @param \Bookshop\BookshopBundle\Entity\CartItems $cartitems
     * @return Cart
     */
    public function addCartitem(\Bookshop\BookshopBundle\Entity\CartItems $cartitems)
    {
        $this->cartitems[] = $cartitems;
    
        return $this;
    }

    /**
     * Remove cartitems
     *
     * @param \Bookshop\BookshopBundle\Entity\CartItems $cartitems
     */
    public function removeCartitem(\Bookshop\BookshopBundle\Entity\CartItems $cartitems)
    {
        $this->cartitems->removeElement($cartitems);
    }

    /**
     * Get cartitems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCartitems()
    {
        return $this->cartitems;
    }
}