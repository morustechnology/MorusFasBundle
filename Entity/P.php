<?php

namespace Morus\FasBundle\Entity;

use Morus\AcceticBundle\Entity\P as BaseP;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class P extends BaseP {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="child_code", type="string", length=50, nullable=true)
     */
    private $childCode;
    
    /**
     * Set childCode
     *
     * @param string $childCode
     * @return P
     */
    public function setChildCode($childCode)
    {
        $this->childCode = $childCode;

        return $this;
    }

    /**
     * Get childCode
     *
     * @return string 
     */
    public function getChildCode()
    {
        return $this->childCode;
    }
}
