<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\Parts as BaseParts;

/**
 * Parts
 *
 * @ORM\Table(name="accetic_parts", uniqueConstraints={@ORM\UniqueConstraint(name="parts_itemcode_index_u", columns={"itemcode"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Parts extends BaseParts
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
     * @ORM\OneToMany(targetEntity="UnitParts", mappedBy="parts", cascade={"persist"})
     **/
    protected $unitParts;
    
    /**
     * Constructor
     * 
     */
    public function __construct() {
        parent::__construct();
        $this->useOthername = false;
    }
    
    /**
     * Set othername
     *
     * @param string $othername
     * @return Parts
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
     * @return Parts
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
     * @return Parts
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
     * Add unitParts
     *
     * @param \Morus\FasBundle\Entity\UnitParts $unitParts
     * @return Parts
     */
    public function addUnitParts(\Morus\FasBundle\Entity\UnitParts $unitParts)
    {
        $this->unitParts[] = $unitParts;

        return $this;
    }

    /**
     * Remove unitParts
     *
     * @param \Morus\FasBundle\Entity\UnitParts $unitParts
     */
    public function removeUnitParts(\Morus\FasBundle\Entity\UnitParts $unitParts)
    {
        $this->unitParts->removeElement($unitParts);
    }

    /**
     * Get unitParts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUnitParts()
    {
        return $this->unitParts;
    }
}
