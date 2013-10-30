<?php

namespace Bookshop\BookshopBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="Bookshop\BookshopBundle\Entity\ProductRepository")
 */
class Product {

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
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank(message="product.add.title.not_blank", groups={"Add", "Edit"})
     */
    private $title;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     * 
     * @Assert\NotBlank(message="product.add.category.not_blank", groups={"Add", "Edit"})
     */
    private $category;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     * 
     * @Assert\NotBlank(message="product.add.price.not_blank", groups={"Add", "Edit"})
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Author")
     * @ORM\JoinColumn(name="author", referencedColumnName="id")
     * 
     * @Assert\NotBlank(message="product.add.author.not_blank", groups={"Add", "Edit"})
     */
    private $author;

    /**
     * @var integer
     *
     * @ORM\Column(name="isbn", type="integer")
     * @Assert\Regex(
     *     pattern="/^\d{10,13}$/",
     *     htmlPattern="*",
     *     match=true,
     *     message="product.isbn.regex",
     *     groups={"Add","Edit"}
     * )
     */
    private $isbn;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer")
     * @Assert\Regex(
     *     pattern="/^[12]\d{3}$/",
     *     htmlPattern="*",
     *     match=true,
     *     message="product.year.regex",
     *     groups={"Add","Edit"}
     * )
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=5000, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="shortdescription", type="string", length=500, nullable=true)
     */
    private $shortdescription;

    /**
     * @var integer
     *
     * @ORM\Column(name="stock", type="integer")
     */
    private $stock;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="integer")
     */
    private $active;

    /**
     * @ORM\OneToOne(targetEntity="Image")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * */
    private $image;
    
     /**
     * @ORM\OneToMany(targetEntity="CartItems", mappedBy="productId")
     */
    private $cartitems;
    

    /**
     * @Assert\File(maxSize = "1M",
     *              mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"},
     *              maxSizeMessage = "The maxmimum allowed file size is 1MB.",
     *              mimeTypesMessage = "Only the filetypes image are allowed.", 
     *              groups={"Add","Edit"})
     */
    private $file;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Product
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
     * Set category
     *
     * @param integer $category
     * @return Product
     */
    public function setCategory($category) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return integer 
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Product
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

    /**
     * Set author
     *
     * @param integer $author
     * @return Product
     */
    public function setAuthor($author) {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return integer 
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * Set isbn
     *
     * @param integer $isbn
     * @return Product
     */
    public function setIsbn($isbn) {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * Get isbn
     *
     * @return integer 
     */
    public function getIsbn() {
        return $this->isbn;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Product
     */
    public function setYear($year) {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear() {
        return $this->year;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set shortdescription
     *
     * @param string $shortdescription
     * @return Product
     */
    public function setShortdescription($shortdescription) {
        $this->shortdescription = $shortdescription;

        return $this;
    }

    /**
     * Get shortdescription
     *
     * @return string 
     */
    public function getShortdescription() {
        return $this->shortdescription;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     * @return Product
     */
    public function setStock($stock) {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer 
     */
    public function getStock() {
        return $this->stock;
    }

    /**
     * Set active
     *
     * @param integer $active
     * @return Product
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
     * Set image
     *
     * @param \Bookshop\BookshopBundle\Entity\Image $image
     * @return Product
     */
    public function setImage(\Bookshop\BookshopBundle\Entity\Image $image = null) {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Bookshop\BookshopBundle\Entity\Image 
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null) {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * 
     *  for image upload
     */
    public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues
        // move takes the target directory and then the
        // target filename to move to
        $filename = $this->getImage()->getFilename();
        
        $this->getFile()->move(
                $this->getUploadRootDir(), 
                $filename
        );

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    public function getAbsolutePath() {
        return null === $this->image->getPath() ? null : $this->getUploadRootDir() . '/' . $this->image->getPath();
    }

    public function getWebPath() {
        return null === $this->image->getPath() ? null : $this->getUploadDir() . '/' . $this->image->getPath();;
    }

    protected function getUploadRootDir() {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/bundles/bookshopbookshop/' . $this->getUploadDir();
    }

    protected function getUploadDir() {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return '/public/image/';
    }

}