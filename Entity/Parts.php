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
     * @var float
     *
     * @ORM\Column(name="default_discount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $defaultDiscount;
    
    /**
     * @ORM\OneToMany(targetEntity="UnitParts", mappedBy="parts", cascade={"persist"})
     **/
    protected $unitParts;
    
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
