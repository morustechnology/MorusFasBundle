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
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    protected $startdate;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    protected $enddate;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="bank_transfer_date", type="datetime", nullable=true)
     */
    protected $banktransferdate;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_print_date", type="datetime", nullable=true)
     */
    protected $lastprintdate;
    
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
    
    /**
     * Set startdate
     *
     * @param \DateTime $startdate
     * @return Ar
     */
    public function setStartdate($startdate)
    {
        $this->startdate = $startdate;

        return $this;
    }

    /**
     * Get startdate
     *
     * @return \DateTime 
     */
    public function getStartdate()
    {
        return $this->startdate;
    }
    
    /**
     * Set enddate
     *
     * @param \DateTime $enddate
     * @return Ar
     */
    public function setEnddate($enddate)
    {
        $this->enddate = $enddate;

        return $this;
    }

    /**
     * Get enddate
     *
     * @return \DateTime 
     */
    public function getEnddate()
    {
        return $this->enddate;
    }
    
    
    
    /**
     * Set banktransferdate
     *
     * @param \DateTime $banktransferdate
     * @return Ar
     */
    public function setBanktransferdate($banktransferdate)
    {
        $this->banktransferdate = $banktransferdate;

        return $this;
    }

    /**
     * Get banktransferdate
     *
     * @return \DateTime 
     */
    public function getBanktransferdate()
    {
        return $this->banktransferdate;
    }
    
    public function getTotal()
    {
        $total = 0;
        foreach($this->transaction->getInvoices() as $invoice){
            $amount = round($invoice->getAmount(),2);
            $total += $amount;
        }
        
        return $total;
    }
    
    /**
     * Set lastprintdate
     *
     * @param \DateTime $lastprintdate
     * @return Ar
     */
    public function setLastprintdate($lastprintdate)
    {
        $this->lastprintdate = $lastprintdate;

        return $this;
    }

    /**
     * Get lastprintdate
     *
     * @return \DateTime 
     */
    public function getLastprintdate()
    {
       return $this->lastprintdate;
    }
    
    public function getUnitName()
    {
        return $this->unit->getName();
    }
    
    
}
