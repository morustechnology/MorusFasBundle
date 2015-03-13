<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\Ar as BaseAr;

/**
 * Ar
 *
 * @ORM\Table(name="accetic_ar", uniqueConstraints={@ORM\UniqueConstraint(name="ar_invnumber_key", columns={"invnumber"})}, indexes={@ORM\Index(name="IDX_unit_id", columns={"unit_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Ar extends BaseAr
{
    /**
     * @ORM\ManyToOne(targetEntity="Export", inversedBy="ars", cascade={"persist"})
     * @ORM\JoinColumn(name="export_id", referencedColumnName="id")
     **/
    protected $export;
    
    /**
     * Set export
     *
     * @param \Morus\FasBundle\Entity\Export $export
     * @return Ar
     */
    public function setExport(\Morus\FasBundle\Entity\Export $export = null)
    {
        $this->export = $export;

        return $this;
    }

    /**
     * Get export
     *
     * @return \Morus\FasBundle\Entity\Export 
     */
    public function getExport()
    {
        return $this->export;
    }
}
