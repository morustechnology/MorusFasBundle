<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\Product as BaseProduct;

/**
 * Product
 *
 * @ORM\Table(name="accetic_product", uniqueConstraints={@ORM\UniqueConstraint(name="product_itemcode_index_u", columns={"itemcode"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Product extends BaseProduct
{   
    /**
     * @var string
     *
     * @ORM\Column(name="other_name", type="string", length=255, nullable=true)
     */
    protected $othername;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="use_other_name", type="boolean")
     */
    protected $useOthername;
    
    /**
     * @var float
     *
     * @ORM\Column(name="default_discount", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $defaultDiscount;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="non_fuel_item", type="boolean", nullable=true)
     */
    protected $nonfuelitem;
    
    /**
     * @ORM\OneToMany(targetEntity="UnitProduct", mappedBy="product", cascade={"persist"})
     **/
    protected $unitProducts;
    
    /**
     * Constructor
     * 
     */
    public function __construct() {
        parent::__construct();
        $this->nonfuelitem = false;
        $this->useOthername = false;
    }
    
    /**
     * Set othername
     *
     * @param string $othername
     * @return Product
     */
    public function setOthername($othername)
    {
        $this->othername = $othername;

        return $this;
    }
    
    /**
     * Get othername
     *
     * @return string 
     */
    public function getOthername()
    {
        return $this->othername;
    }
    
    /**
     * Set useOthername
     *
     * @param boolean $useOthername
     * @return Product
     */
    public function setUseOthername($useOthername)
    {
        $this->useOthername = $useOthername;

        return $this;
    }

    /**
     * Get useOthername
     *
     * @return boolean 
     */
    public function getUseOthername()
    {
        return $this->useOthername;
    }
    
    /**
     * Set defaultDiscount
     *
     * @param float $defaultDiscount
     * @return Product
     */
    public function setDefaultDiscount($defaultDiscount)
    {
        $this->defaultDiscount = $defaultDiscount;

        return $this;
    }

    /**
     * Get defaultDiscount
     *
     * @return float 
     */
    public function getDefaultDiscount()
    {
        return $this->defaultDiscount;
    }
    
    /**
     * Set nonfuelitem
     *
     * @param boolean $nonfuelitem
     * @return Product
     */
    public function setNonfuelitem($nonfuelitem)
    {
        $this->nonfuelitem = $nonfuelitem;

        return $this;
    }

    /**
     * Get nonfuelitem
     *
     * @return boolean 
     */
    public function getNonfuelitem()
    {
        return $this->nonfuelitem;
    }
    
    /**
     * Add unitProduct
     *
     * @param \Morus\FasBundle\Entity\UnitProduct $unitProduct
     * @return Product
     */
    public function addUnitProduct(\Morus\FasBundle\Entity\UnitProduct $unitProduct)
    {
        $this->unitProducts[] = $unitProduct;

        return $this;
    }

    /**
     * Remove unitProduct
     *
     * @param \Morus\FasBundle\Entity\UnitProduct $unitProduct
     */
    public function removeUnitProduct(\Morus\FasBundle\Entity\UnitProduct $unitProduct)
    {
        $this->unitProducts->removeElement($unitProduct);
    }

    /**
     * Get unitProducts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUnitProducts()
    {
        return $this->unitProducts;
    }
}
