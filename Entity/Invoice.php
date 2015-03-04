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
    private $cardNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="trans_date", type="date", nullable=false)
     */
    private $transDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="trans_time", type="date", nullable=false)
     */
    private $transTime;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=255, nullable=false)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="receipt_number", type="string", length=255, nullable=false)
     */
    private $receiptNumber;
    
    /**
     * @var
     * 
     * @ORM\Column(name="cost", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $cost;
    
    /**
     * Set cardNumber
     *
     * @param string $cardNumber
     * @return InvoiceExt
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
     * @return InvoiceExt
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
     * @return InvoiceExt
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
     * @return InvoiceExt
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
     * Set receiptNumber
     *
     * @param string $receiptNumber
     * @return InvoiceExt
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
     * Set cost
     *
     * @param string $cost
     * @return Invoice
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return string 
     */
    public function getCost()
    {
        return $this->cost;
    }
}
