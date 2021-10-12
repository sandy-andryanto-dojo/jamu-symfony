<?php

namespace AppBundle\Model;

use Doctrine\ORM\Mapping as ORM;


/**
 * Brand
 *
 * @ORM\Entity
 * @ORM\Table(name="brands")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BrandRepository")
 * 
 */
class Brand{


	public function __construct(){
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
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description",type="text",nullable=true)
     */
    protected $description;

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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

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

    public function __toString() {
        return $this->name;
    }
}