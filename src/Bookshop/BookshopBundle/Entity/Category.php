<?php

namespace Bookshop\BookshopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Tool\Wrapper\EntityWrapper;
use Gedmo\Translatable\Translatable;
/**
 * Category
 *
 * @ORM\Table(name = "categories")
 * @ORM\Entity(repositoryClass="Bookshop\BookshopBundle\Entity\CategoryRepository")
 * 
 * @Gedmo\TranslationEntity(class="Bookshop\BookshopBundle\Entity\Translation\CategoryTranslation")
 */
class Category implements Translatable
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="category")
     */
    protected $products;
    
    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add products
     *
     * @param \Bookshop\BookshopBundle\Entity\Product $products
     * @return Category
     */
    public function addProduct(\Bookshop\BookshopBundle\Entity\Product $products) {
        $this->products[] = $products;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \Bookshop\BookshopBundle\Entity\Product $products
     */
    public function removeProduct(\Bookshop\BookshopBundle\Entity\Product $products) {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts() {
        return $this->products;
    }
    
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
    public function getLocale()
    {
        return $this->locale;
    }


    public function __toString() {
        return $this->name;
    }

}