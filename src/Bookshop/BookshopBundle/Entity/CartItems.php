<?php

namespace Bookshop\BookshopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CartItems
 *
 * @ORM\Table(name="cart_items")
 * @ORM\Entity(repositoryClass="Bookshop\BookshopBundle\Entity\CartItemsRepository")
 */
class CartItems {

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
     * @ORM\ManyToOne(targetEntity="Cart", inversedBy="cartitems")
     * @ORM\JoinColumn(name="cart_Id", referencedColumnName="id")
     */
    private $cartId;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="cartitems")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $productId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set cartId
     *
     * @param integer $cartId
     * @return CartItems
     */
    public function setCartId($cartId) {
        $this->cartId = $cartId;

        return $this;
    }

    /**
     * Get cartId
     *
     * @return integer 
     */
    public function getCartId() {
        return $this->cartId;
    }

    /**
     * Set productId
     *
     * @param integer $productId
     * @return CartItems
     */
    public function setProductId($productId) {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer 
     */
    public function getProductId() {
        return $this->productId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return CartItems
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return CartItems
     */
    public function setQuantity($quantity) {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return CartItems
     */
    public function setPrice($price) {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice() {
        return $this->price;
    }

}
