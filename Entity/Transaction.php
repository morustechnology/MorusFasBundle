<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\Transaction as BaseTransaction;

/**
 * Transaction
 *
 * @ORM\Table(name="accetic_transaction")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Transaction extends BaseTransaction
{
    /**
     * @ORM\ManyToOne(targetEntity="Export", inversedBy="transactions", cascade={"persist"})
     * @ORM\JoinColumn(name="export_id", referencedColumnName="id")
     **/
    protected $export;
    
    /**
     * Set export
     *
     * @param \Morus\FasBundle\Entity\Export $export
     * @return Transaction
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
