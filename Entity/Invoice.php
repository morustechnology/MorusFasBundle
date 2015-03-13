<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\Invoice as BaseInvoice;

/**
 * Invoice
 *
 * @ORM\Table(name="accetic_invoice", indexes={@ORM\Index(name="invoice_trans_id_key", columns={"transaction_id"}), @ORM\Index(name="IDX_parts_id", columns={"parts_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Invoice extends BaseInvoice
{
    /**
     * @var string
     *
     * @ORM\Column(name="card_number", type="string", length=100, nullable=false)
     */
    protected $cardNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="trans_date", type="date", nullable=false)
     */
    protected $transDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="trans_time", type="date", nullable=false)
     */
    protected $transTime;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=255, nullable=false)
     */
    protected $site;

    /**
     * @var string
     *
     * @ORM\Column(name="licence", type="string", length=255, nullable=false)
     */
    protected $licence;
    
    /**
     * @var string
     *
     * @ORM\Column(name="receipt_number", type="string", length=255, nullable=false)
     */
    protected $receiptNumber;
    
    /**
     * @var
     * 
     * @ORM\Column(name="unit_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $unitprice;
    
    /**
     * @var
     * 
     * @ORM\Column(name="unit_discount", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $unitdiscount;
    
    /**
     * @var
     * 
     * @ORM\Column(name="net_amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $netamount;
    
    /**
     * @var
     * 
     * @ORM\Column(name="sell_discount", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $selldiscount;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="customer_discount", type="boolean")
     */
    protected $customerdiscount;
    
    /**
     * constructor
     */
    public function __construct() {
        parent::__construct();
        $this->customerdiscount = false;
    }
    
    /**
     * Set cardNumber
     *
     * @param string $cardNumber
     * @return Invoice
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    /**
     * Get cardNumber
     *
     * @return string 
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * Set transDate
     *
     * @param \DateTime $transDate
     * @return Invoice
     */
    public function setTransDate($transDate)
    {
        $this->transDate = $transDate;

        return $this;
    }

    /**
     * Get transDate
     *
     * @return \DateTime 
     */
    public function getTransDate()
    {
        return $this->transDate;
    }

    /**
     * Set transTime
     *
     * @param \DateTime $transTime
     * @return Invoice
     */
    public function setTransTime($transTime)
    {
        $this->transTime = $transTime;

        return $this;
    }

    /**
     * Get transTime
     *
     * @return \DateTime 
     */
    public function getTransTime()
    {
        return $this->transTime;
    }

    /**
     * Set site
     *
     * @param string $site
     * @return Invoice
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return string 
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set licence
     *
     * @param string $licence
     * @return Invoice
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get licence
     *
     * @return string 
     */
    public function getLicence()
    {
        return $this->licence;
    }
    
    /**
     * Set receiptNumber
     *
     * @param string $receiptNumber
     * @return Invoice
     */
    public function setReceiptNumber($receiptNumber)
    {
        $this->receiptNumber = $receiptNumber;

        return $this;
    }

    /**
     * Get receiptNumber
     *
     * @return string 
     */
    public function getReceiptNumber()
    {
        return $this->receiptNumber;
    }
    
    /**
     * Set unitprice
     *
     * @param string $unitprice
     * @return Invoice
     */
    public function setUnitprice($unitprice)
    {
        $this->unitprice = $unitprice;

        return $this;
    }

    /**
     * Get unitprice
     *
     * @return string 
     */
    public function getUnitprice()
    {
        return $this->unitprice;
    }
    
    /**
     * Set unitdiscount
     *
     * @param string $unitdiscount
     * @return Invoice
     */
    public function setUnitdiscount($unitdiscount)
    {
        $this->unitdiscount = $unitdiscount;

        return $this;
    }

    /**
     * Get unitdiscount
     *
     * @return float 
     */
    public function getUnitdiscount()
    {
        return $this->unitdiscount;
    }
    
    /**
     * Set netamount
     *
     * @param string $netamount
     * @return Invoice
     */
    public function setNetamount($netamount)
    {
        $this->netamount = $netamount;

        return $this;
    }

    /**
     * Get netamount
     *
     * @return float 
     */
    public function getNetamount()
    {
        return $this->netamount;
    }
    
    /**
     * Set selldiscount
     *
     * @param string $selldiscount
     * @return Invoice
     */
    public function setSelldiscount($selldiscount)
    {
        $this->selldiscount = $selldiscount;

        return $this;
    }

    /**
     * Get selldiscount
     *
     * @return float 
     */
    public function getSelldiscount()
    {
        return $this->selldiscount;
    }
    
    /**
     * Set customerdiscount
     *
     * @param boolean $customerdiscount
     * @return Statement
     */
    public function setCustomerdiscount($customerdiscount)
    {
        $this->customerdiscount = $customerdiscount;

        return $this;
    }

    /**
     * Get customerdiscount
     *
     * @return boolean 
     */
    public function getCustomerdiscount()
    {
        return $this->customerdiscount;
    }
}
