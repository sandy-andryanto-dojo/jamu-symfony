<?php

namespace AppBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Brand
 *
 * @ORM\Entity
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 * 
 */

class Product{

	public function __construct() {
        $this->categories = new ArrayCollection();
        $this->setCreatedAt(new \DateTime());
    }


	/**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name",type="string",nullable=false)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="code",type="string",nullable=false)
     */
    protected $name;

    /**
     * @var float
     *
     * @ORM\Column(name="price",type="float",options={"default":0})
     * @Assert\NotBlank()
     */
    protected $price;


     /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Model\Brand")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    protected $brand;


    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Model\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Assert\Count(min="1")
     * 
     */
    protected $categories;


    /**
     * @var string
     *
     * @ORM\Column(name="image",type="string",nullable=true)
     * 
     */
    protected $image;


    /**
     * @var date $createdAt
     *
     * @ORM\Column(name="expired", type="date",nullable=true)
     */
    protected $expired;


    /**
     * @var date $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return self
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     *
     * @return self
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param brand $brand
     *
     * @return self
     */
    public function setBrand(brand $brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     *
     * @return self
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     *
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return date $createdAt
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * @param date $createdAt $expired
     *
     * @return self
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * @return date $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param date $createdAt $createdAt
     *
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}