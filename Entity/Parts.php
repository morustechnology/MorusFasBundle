<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\Parts as BaseParts;

/**
 * Parts
 *
 * @ORM\Table(name="accetic_parts", uniqueConstraints={@ORM\UniqueConstraint(name="parts_itemcode_index_u", columns={"itemcode"})})
 * @ORM\Entity(repositoryClass="Morus\AcceticBundle\Entity\Repository\PartsRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Parts extends BaseParts
{
    /**
     * @var float
     *
     * @ORM\Column(name="default_discount", type="decimal")
     */
    private $defaultDiscount;
    
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
}
