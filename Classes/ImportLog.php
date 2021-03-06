<?php

namespace Morus\FasBundle\Classes;

/**
 * Statement Import Result Log
 *
 * @author Michael
 */
class ImportLog {
    
    const EMPTY_NULL_VALUE = 'error.import.empty_null_value';
    const INVALID_DATETIME_VALUE = 'error.import.invalid_datetime_value';
    const INVALID_DATE_VALUE = 'error.import.invalid_date_value';
    const INVALID_TIME_VALUE = 'error.import.invalid_time_value';
    
    /**
     * @var string
     */
    private $row;
    
    /**
     * @var boolean
     */
    private $nullCardNumber;
            
    /**
     * @var boolean
     */
    private $nullLicenceNumber;
    
    /**
     * @var boolean
     */
    private $nullSite;
            
    /**
     * @var boolean
     */
    private $nullReceiptNumber;
    
    /**
     * @var boolean
     */
    private $nullTransactionDateTime;
    
    /**
     * @var boolean
     */
    private $nullTransactionDate;
    
    /**
     * @var boolean
     */
    private $nullTransactionTime;
    
    /**
     * @var boolean
     */
    private $invalidTransactionDateTime;
    
    /**
     * @var boolean
     */
    private $invalidTransactionDate;
    
    /**
     * @var boolean
     */
    private $invalidTransactionTime;
    
    /**
     * @var boolean
     */
    private $nullProductName;
    
    /**
     * @var boolean
     */
    private $nullUnitDiscount;
    
    /**
     * @var boolean
     */
    private $nullVolume;
    
    /**
     * @var boolean
     */
    private $nullUnitPrice;
    
    /**
     * @var boolean
     */
    private $nullNetAmount;
    
    
    /**
     * Constructor
     */
    public function __construct($row)
    {
        $this->row = $row;
    }
        
    /**
     * Set log
     *
     * @param boolean $nullCardNum, boolean $nullLicNum
     * @return ImportLog
     */
    public function setLog($nullCardNumber, $nullLicenceNumber, $nullSite, $nullReceiptNumber, $nullTransactionDateTime,
            $nullTransactionDate, $nullTransactionTime, $nullProductName, $nullUnitDiscount, $nullVolume, $nullUnitPrice, 
            $nullNetAmount, $invalidTransactionDateTime, $invalidTransactionDate, $invalidTransactionTime)
    {
        $this->nullCardNumber = $nullCardNumber;
        $this->nullLicenceNumber = $nullLicenceNumber;
        $this->nullSite = $nullSite;
        $this->nullReceiptNumber = $nullReceiptNumber;
        $this->nullTransactionDateTime = $nullTransactionDateTime;
        $this->nullTransactionDate = $nullTransactionDate;
        $this->nullTransactionTime = $nullTransactionTime;
        $this->nullProductName = $nullProductName;
        $this->nullVolume = $nullVolume;
        $this->nullUnitPrice = $nullUnitPrice;
        $this->nullNetAmount = $nullNetAmount;
        $this->invalidTransactionDateTime = $invalidTransactionDateTime;
        $this->invalidTransactionDate = $invalidTransactionDate;
        $this->invalidTransactionTime = $invalidTransactionTime;
        
        return $this;
    }
    
    /**
     * Get row
     *
     * @return integer 
     */
    public function getRow()
    {
        return $this->row;
    }
    
    /**
     * Get nullCardNumber
     *
     * @return boolean 
     */
    public function getNullCardNumber()
    {
        return $this->nullCardNumber;
    }
    
   
    
    /**
     * Get nullLicenceNumber
     *
     * @return boolean 
     */
    public function getNullLicenceNumber()
    {
        return $this->nullLicenceNumber;
    }
    
    /**
     * Get nullSite
     *
     * @return boolean
     */
    public function getNullSite()
    {
        return $this->nullSite;
    }
            
    /**
     * Get nullReceiptNumber
     *
     * @return boolean
     */
    public function getNullReceiptNumber()
    {
        return $this->nullReceiptNumber;
    }
    
    /**
     * Get nullTransactionDateTime
     *
     * @return boolean
     */
    public function getNullTransactionDateTime()
    {
        return $this->nullTransactionDateTime;
    }
    
    /**
     * Get nullTransactionDate
     *
     * @return boolean
     */
    public function getNullTransactionDate()
    {
        return $this->nullTransactionDate;
    }
    
    /**
     * Get nullTransactionTime
     *
     * @return boolean
     */
    public function getNullTransactionTime()
    {
        return $this->nullTransactionTime;
    }
    
    /**
     * Get invalidTransactionDateTime
     *
     * @return boolean
     */
    public function getInvalidTransactionDateTime()
    {
        return $this->invalidTransactionDateTime;
    }
    
    /**
     * Get invalidTransactionDate
     *
     * @return boolean
     */
    public function getInvalidTransactionDate()
    {
        return $this->invalidTransactionDate;
    }
    
    /**
     * Get invalidTransactionTime
     *
     * @return boolean
     */
    public function getInvalidTransactionTime()
    {
        return $this->invalidTransactionTime;
    }
    
    /**
     * Get nullProductName
     *
     * @return boolean
     */
    public function getNullProductName()
    {
        return $this->nullProductName;
    }
    
    /**
     * Get nullUnitDiscount
     *
     * @return boolean
     */
    public function getNullUnitDiscount()
    {
        return $this->nullUnitDiscount;
    }
    
    /**
     * Get nullVolume
     *
     * @return boolean
     */
    public function getNullVolume()
    {
        return $this->nullVolume;
    }
    
    /**
     * Get nullUnitPrice
     *
     * @return boolean
     */
    public function getNullUnitPrice()
    {
        return $this->nullUnitPrice;
    }
    
    /**
     * Get nullNetAmount
     *
     * @return boolean
     */
    public function getNullNetAmount()
    {
        return $this->nullNetAmount;
    }
}
