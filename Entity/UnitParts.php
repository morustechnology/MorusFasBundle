<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UnitParts
 *
 * @ORM\Table(name="accetic_unit_parts")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class UnitParts {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="decimal", precision=10, scale=2)
     */
    protected $discount;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=true)
     */
    protected $sortOrder;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    protected $createDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_modified_date", type="datetime", nullable=true)
     */
    protected $lastModifiedDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="inactive_date", type="datetime", nullable=true)
     */
    protected $inactiveDate;
    
    /**
     * @ORM\ManyToOne(targetEntity="Unit", inversedBy="unitParts", cascade={"persist"})
     * @ORM\JoinColumn(name="unit_id", referencedColumnName="id")
     **/
    protected $unit;
    
    /**
     * @ORM\ManyToOne(targetEntity="Parts", inversedBy="unitParts", cascade={"persist"})
     * @ORM\JoinColumn(name="parts_id", referencedColumnName="id")
     **/
    protected $parts;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createDate = new \DateTime("now");
        $this->active = true;
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set discount
     *
     * @param float $discount
     * @return UnitParts
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return float 
     */
    public function getDiscount()
    {
        return $this->discount;
    }
    
    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     * @return UnitParts
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder
     *
     * @return integer 
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return UnitParts
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return UnitParts
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set lastModifiedDate
     *
     * @param \DateTime $lastModifiedDate
     * @return UnitParts
     */
    public function setLastModifiedDate($lastModifiedDate)
    {
        $this->lastModifiedDate = $lastModifiedDate;

        return $this;
    }

    /**
     * Get lastModifiedDate
     *
     * @return \DateTime 
     */
    public function getLastModifiedDate()
    {
        return $this->lastModifiedDate;
    }

    /**
     * Set inactiveDate
     *
     * @param \DateTime $inactiveDate
     * @return UnitParts
     */
    public function setInactiveDate($inactiveDate)
    {
        $this->inactiveDate = $inactiveDate;

        return $this;
    }

    /**
     * Get inactiveDate
     *
     * @return \DateTime 
     */
    public function getInactiveDate()
    {
        return $this->inactiveDate;
    }
    
    /**
     * Set unit
     *
     * @param \Morus\FasBundle\Entity\Unit $unit
     * @return UnitParts
     */
    public function setUnit(\Morus\FasBundle\Entity\Unit $unit = null)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return \Morus\FasBundle\Entity\Unit 
     */
    public function getUnit()
    {
        return $this->unit;
    }
    
    /**
     * Set parts
     *
     * @param \Morus\FasBundle\Entity\Parts $parts
     * @return UnitParts
     */
    public function setParts(\Morus\FasBundle\Entity\Parts $parts = null)
    {
        $this->parts = $parts;

        return $this;
    }

    /**
     * Get parts
     *
     * @return \Morus\FasBundle\Entity\Parts 
     */
    public function getParts()
    {
        return $this->parts;
    }
}
