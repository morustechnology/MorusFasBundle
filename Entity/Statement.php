<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Morus\FasBundle\Classes\HeaderKey;

/**
 * Statement Status
 *
 * @ORM\Table(name="fas_statement")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Statement
{
    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;
    
    /**
     *
     * @var file
     */
    private $temp;
    
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="mine_type", type="string", length=255, nullable=true)
     */
    protected $mineType;
    
    /**
     * @var string
     *
     * @ORM\Column(name="path", type="text", nullable=true)
     */
    protected $path;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="splitDateTime", type="boolean", nullable=false)
     */
    protected $splitDateTime;
    
    /**
     * @var string
     *
     * @ORM\Column(name="datetime_format", type="string", length=50, nullable=true)
     */
    protected $datetimeFormat;
    
    /**
     * @var string
     *
     * @ORM\Column(name="date_format", type="string", length=50, nullable=true)
     */
    protected $dateFormat;
    
    /**
     * @var string
     *
     * @ORM\Column(name="time_format", type="string", length=50, nullable=true)
     */
    protected $timeFormat;
    
    /**
     * @var string
     *
     * @ORM\Column(name="headers", type="text", nullable=false)
     */
    protected $headers;

    /**
     * @var string
     *
     * @ORM\Column(name="key_pairs", type="text", nullable=false)
     */
    protected $keyPairs;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=true)
     */
    protected $sortOrder;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    protected $createDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_modified_date", type="datetime", nullable=true)
     */
    protected $lastModifiedDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="inactive_date", type="datetime", nullable=true)
     */
    protected $inactiveDate;

    /**
     * @ORM\ManyToOne(targetEntity="StatementStatus", inversedBy="statements", cascade={"persist"})
     * @ORM\JoinColumn(name="statement_status_id", referencedColumnName="id")
     **/
    protected $statementStatus;

    /**
     * @ORM\ManyToOne(targetEntity="Unit", inversedBy="statements", cascade={"persist"})
     * @ORM\JoinColumn(name="unit_id", referencedColumnName="id")
     **/
    protected $unit;
    
    /**
     * @ORM\ManyToOne(targetEntity="Export", inversedBy="statements", cascade={"persist"})
     * @ORM\JoinColumn(name="export_id", referencedColumnName="id")
     **/
    protected $export;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $month = date('m', strtotime('-1 month'));
        $year = date('Y', strtotime('-1 month'));
        
//        $datetime = new \DateTime("now");
//        $datetimeStr = $datetime->format('d-m-Y');
//        $this->name = 'Import ' . $datetimeStr;
        $this->name = $month.'月份 '.$year;
        $this->splitDateTime = false;
        $this->datetimeFormat = 'd/m/Y H:i:s';
        $this->dateFormat = 'd/m/Y';
        $this->timeFormat = 'H:i:s';
        $this->createDate = new \DateTime("now");
        $this->active = true;
    }
    
    /**
     * Set cardNumberHeader
     *
     * @param string $cardNumberHeader
     * @return Statement
     */
    public function setCardNumberHeader($cardNumberHeader)
    {
        $this->setCodeHeader(HeaderKey::CARD_NUMBER, $cardNumberHeader);

        return $this;
    }

    /**
     * Get cardNumberHeader
     *
     * @return string 
     */
    public function getCardNumberHeader()
    {
        return $this->getCodeHeader(HeaderKey::CARD_NUMBER);
    }
    
    /**
     * Set licenceNumberHeader
     *
     * @param string $licenceNumberHeader
     * @return Statement
     */
    public function setLicenceNumberHeader($licenceNumberHeader)
    {
        $this->setCodeHeader(HeaderKey::LICENCE_NUMBER, $licenceNumberHeader);

        return $this;
    }

    /**
     * Get licenceNumberHeader
     *
     * @return string 
     */
    public function getLicenceNumberHeader()
    {
        return $this->getCodeHeader(HeaderKey::LICENCE_NUMBER);
    }
    
    /**
     * Set siteHeader
     *
     * @param string $siteHeader
     * @return Statement
     */
    public function setSiteHeader($siteHeader)
    {
        $this->setCodeHeader(HeaderKey::SITE, $siteHeader);

        return $this;
    }

    /**
     * Get siteHeader
     *
     * @return string 
     */
    public function getSiteHeader()
    {
        return $this->getCodeHeader(HeaderKey::SITE);
    }
    
    /**
     * Set receiptNumberHeader
     *
     * @param string $receiptNumberHeader
     * @return Statement
     */
    public function setReceiptNumberHeader($receiptNumberHeader)
    {
        $this->setCodeHeader(HeaderKey::RECEIPT_NUMBER, $receiptNumberHeader);

        return $this;
    }

    /**
     * Get receiptNumberHeader
     *
     * @return string 
     */
    public function getReceiptNumberHeader()
    {
        return $this->getCodeHeader(HeaderKey::RECEIPT_NUMBER);
    }
    
    /**
     * Set transactionDatetimeHeader
     *
     * @param string $transactionDatetimeHeader
     * @return Statement
     */
    public function setTransactionDatetimeHeader($transactionDatetimeHeader)
    {
        $this->setCodeHeader(HeaderKey::TRANSACTION_DATETIME, $transactionDatetimeHeader);

        return $this;
    }

    /**
     * Get transactionDatetimeHeader
     *
     * @return string 
     */
    public function getTransactionDatetimeHeader()
    {
        return $this->getCodeHeader(HeaderKey::TRANSACTION_DATETIME);
    }
    
    /**
     * Set transactionDateHeader
     *
     * @param string $transactionDateHeader
     * @return Statement
     */
    public function setTransactionDateHeader($transactionDateHeader)
    {
        $this->setCodeHeader(HeaderKey::TRANSACTION_DATE, $transactionDateHeader);

        return $this;
    }

    /**
     * Get transactionDateHeader
     *
     * @return string 
     */
    public function getTransactionDateHeader()
    {
        return $this->getCodeHeader(HeaderKey::TRANSACTION_DATE);
    }
    
    /**
     * Set transactionTimeHeader
     *
     * @param string $transactionTimeHeader
     * @return Statement
     */
    public function setTransactionTimeHeader($transactionTimeHeader)
    {
        $this->setCodeHeader(HeaderKey::TRANSACTION_TIME, $transactionTimeHeader);

        return $this;
    }

    /**
     * Get transactionTimeHeader
     *
     * @return string 
     */
    public function getTransactionTimeHeader()
    {
        return $this->getCodeHeader(HeaderKey::TRANSACTION_TIME);
    }
    
    /**
     * Set productNameHeader
     *
     * @param string $productNameHeader
     * @return Statement
     */
    public function setProductNameHeader($productNameHeader)
    {
        $this->setCodeHeader(HeaderKey::PRODUCT_NAME, $productNameHeader);

        return $this;
    }

    /**
     * Get productNameHeader
     *
     * @return string 
     */
    public function getProductNameHeader()
    {
        return $this->getCodeHeader(HeaderKey::PRODUCT_NAME);
    }
    
    /**
     * Set unitDiscountHeader
     *
     * @param string $unitDiscountHeader
     * @return Statement
     */
    public function setUnitDiscountHeader($unitDiscountHeader)
    {
        $this->setCodeHeader(HeaderKey::UNIT_DISCOUNT, $unitDiscountHeader);

        return $this;
    }

    /**
     * Get unitDiscountHeader
     *
     * @return string 
     */
    public function getUnitDiscountHeader()
    {
        return $this->getCodeHeader(HeaderKey::UNIT_DISCOUNT);
    }
    
    /**
     * Set volumeHeader
     *
     * @param string $volumeHeader
     * @return Statement
     */
    public function setVolumeHeader($volumeHeader)
    {
        $this->setCodeHeader(HeaderKey::VOLUME, $volumeHeader);

        return $this;
    }

    /**
     * Get volumeHeader
     *
     * @return string 
     */
    public function getVolumeHeader()
    {
        return $this->getCodeHeader(HeaderKey::VOLUME);
    }
    
    /**
     * Set unitPriceHeader
     *
     * @param string $unitPriceHeader
     * @return Statement
     */
    public function setUnitPriceHeader($unitPriceHeader)
    {
        $this->setCodeHeader(HeaderKey::UNIT_PRICE, $unitPriceHeader);

        return $this;
    }

    /**
     * Get unitPriceHeader
     *
     * @return string 
     */
    public function getUnitPriceHeader()
    {
        return $this->getCodeHeader(HeaderKey::UNIT_PRICE);
    }
    
    /**
     * Set netAmountHeader
     *
     * @param string $netAmountHeader
     * @return Statement
     */
    public function setNetAmountHeader($netAmountHeader)
    {
        $this->setCodeHeader(HeaderKey::NET_AMOUNT, $netAmountHeader);

        return $this;
    }

    /**
     * Get netAmountHeader
     *
     * @return string 
     */
    public function getNetAmountHeader()
    {
        return $this->getCodeHeader(HeaderKey::NET_AMOUNT);
    }
    
    private function setCodeHeader($key, $header) {
        $ary = json_decode($this->keyPairs, true);
        $ary[$key] = $header;
        $this->keyPairs = json_encode($ary);
    }
    
    private function getCodeHeader($key) {
        $ary = json_decode($this->keyPairs, true);
        
        if ($ary && array_key_exists($key,$ary)) {
            return $ary[$key];
        } else {
            return null;
        }
    }
    
    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        $fileInfo = $this->getFile()->getFileInfo();
        $this->mineType = mime_content_type($fileInfo->getPathname());
        
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
    
    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Statement
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set mineType
     *
     * @param string $mineType
     * @return Statement
     */
    public function setMineType($mineType)
    {
        $this->mineType = $mineType;

        return $this;
    }

    /**
     * Get mineType
     *
     * @return string 
     */
    public function getMineType()
    {
        return $this->mineType;
    }
    
    /**
     * Set path
     *
     * @param string $path
     * @return Statement
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * Set splitDateTime
     *
     * @param boolean $splitDateTime
     * @return Statement
     */
    public function setSplitDateTime($splitDateTime)
    {
        $this->splitDateTime = $splitDateTime;

        return $this;
    }

    /**
     * Get splitDateTime
     *
     * @return boolean 
     */
    public function getSplitDateTime()
    {
        return $this->splitDateTime;
    }
    
    /**
     * Set datetimeFormat
     *
     * @param string $datetimeFormat
     * @return Statement
     */
    public function setDatetimeFormat($datetimeFormat)
    {
        $this->datetimeFormat = $datetimeFormat;

        return $this;
    }

    /**
     * Get datetimeFormat
     *
     * @return string 
     */
    public function getDatetimeFormat()
    {
        return $this->datetimeFormat;
    }
    
    /**
     * Set dateFormat
     *
     * @param string $dateFormat
     * @return Statement
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    /**
     * Get dateFormat
     *
     * @return string 
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }
    
    /**
     * Set timeFormat
     *
     * @param string $timeFormat
     * @return Statement
     */
    public function setTimeFormat($timeFormat)
    {
        $this->timeFormat = $timeFormat;

        return $this;
    }

    /**
     * Get timeFormat
     *
     * @return string 
     */
    public function getTimeFormat()
    {
        return $this->timeFormat;
    }
    
    /**
     * Set headers
     *
     * @param array $headers
     * @return Statement
     */
    public function setHeaders($headers)
    {
        $this->headers = json_encode($headers);

        return $this;
    }

    /**
     * Get headers
     *
     * @return array 
     */
    public function getHeaders()
    {
        return json_decode($this->headers, true);
    }

    /**
     * Set keyPairs ( key => column index )
     *
     * @param array $keyPairs
     * @return Statement
     */
    public function setKeyPairs($keyPairs)
    {
        $this->keyPairs = json_encode($keyPairs);

        return $this;
    }

    /**
     * Get keyPairs ( key => column index )
     *
     * @return array
     */
    public function getKeyPairs()
    {
        return json_decode($this->keyPairs, true);
    }
    
    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     * @return Statement
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder
     *
     * @return integer 
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Statement
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Statement
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set lastModifiedDate
     *
     * @param \DateTime $lastModifiedDate
     * @return Statement
     */
    public function setLastModifiedDate($lastModifiedDate)
    {
        $this->lastModifiedDate = $lastModifiedDate;

        return $this;
    }

    /**
     * Get lastModifiedDate
     *
     * @return \DateTime 
     */
    public function getLastModifiedDate()
    {
        return $this->lastModifiedDate;
    }

    /**
     * Set inactiveDate
     *
     * @param \DateTime $inactiveDate
     * @return Statement
     */
    public function setInactiveDate($inactiveDate)
    {
        $this->inactiveDate = $inactiveDate;

        return $this;
    }

    /**
     * Get inactiveDate
     *
     * @return \DateTime 
     */
    public function getInactiveDate()
    {
        return $this->inactiveDate;
    }

    /**
     * Set statementStatus
     *
     * @param \Morus\FasBundle\Entity\StatementStatus $statementStatus
     * @return Statement
     */
    public function setStatementStatus(\Morus\FasBundle\Entity\StatementStatus $statementStatus = null)
    {
        $this->statementStatus = $statementStatus;

        return $this;
    }

    /**
     * Get statementStatus
     *
     * @return \Morus\FasBundle\Entity\StatementStatus 
     */
    public function getStatementStatus()
    {
        return $this->statementStatus;
    }

    /**
     * Set unit
     *
     * @param \Morus\FasBundle\Entity\Unit $unit
     * @return Statement
     */
    public function setUnit(\Morus\FasBundle\Entity\Unit $unit = null)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return \Morus\FasBundle\Entity\Unit 
     */
    public function getUnit()
    {
        return $this->unit;
    }
    
    /**
     * Set export
     *
     * @param \Morus\FasBundle\Entity\Export $export
     * @return Statement
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
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $mimeType = $this->getFile()->getFileInfo();
            
//            $this->path = $filename.'.'.$this->getFile()->guessExtension();
            $this->path = $filename.'.csv';
        }
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->path);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PostRemove
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if ($file) {
            unlink($file);
        }
    }
}
