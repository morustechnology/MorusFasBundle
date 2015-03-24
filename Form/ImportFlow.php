<?php

namespace Morus\FasBundle\Form;


use Craue\FormFlowBundle\Form\FormFlow;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Reader\ExcelReader;
use SplFileObject;
use Morus\FasBundle\Classes\ImportLog;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Morus\FasBundle\Classes\HeaderKey;
use Morus\FasBundle\Entity\FasConfig;

class ImportFlow extends FormFlow {
    
    protected $allowDynamicStepNavigation = true;
    protected $handleFileUploads = true;
    

    protected $entityManager;
    protected $container;
    protected $reader;

    public $sample = array();
    public $isSplitDateTime = false;
    public $hasError = false;
    public $totalProcessedCount;
    public $errorLogs = array();
    public $newProductList = array();
    
    public function __construct(EntityManager $entityManager, Container $container) {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }
    
    protected function loadStepsConfig() {
        return array(
            array(
                'label' => 'statement.upload',
                'type' => new Type\ImportStep1Type(),
            ),
            array(
                'label' => 'statement.pairing_header',
                'type' => new Type\ImportStep2Type(),
            ),
            array(
                'label' => 'statement.confirmation',
            ),
        );
    }
    
    public function getFormOptions($step, array $options = array()) {
        $options = parent::getFormOptions($step, $options);
        $statement = $this->getFormData();
        $this->hasError = false;
        switch($step) {
            case 2:
                if ($statement->getFile()) {  
                    $this->analyseHeader($statement);
                }
                break;
            case 3:
                if ($statement->getKeyPairs()) {
                    $this->validateStatement($statement);
                }
                break;
            case 4:
                break;
        }
        
        return $options;
    }
    
    private function analyseHeader($statement) {
        
        // 1. Save header in array
        $file = new SplFileObject($statement->getFile());
        $mineType = mime_content_type($file->getFileInfo()->getPathname());
        
        if ($mineType == 'application/vnd.ms-excel') {
            $this->reader = new ExcelReader($file);
        } else {
            $this->reader = new CsvReader($file);
        }
        
        // Get headers and store in statment
        $this->reader->setHeaderRowNumber(0);
        $headerAry = array();
        foreach($this->reader->getColumnHeaders() as $h) {
            $headerAry[$h] = $h;
        }
        $statement->setHeaders($headerAry);
        
        // get split datetime option and pass to view
        $this->isSplitDateTime = $statement->getSplitDateTime();
        
        // 2. Get last header pairing record and set to statement
        $pairingControlCode = HeaderKey::STATEMENT_PAIRING_HISTORY.'_'.strtoupper($statement->getUnit()->getId());
        $pairingConfig = $this->entityManager
                    ->getRepository('MorusFasBundle:FasConfig')
                    ->findOneByControlCode($pairingControlCode);
        
        if ($pairingConfig && $pairingConfig->getValue()) {
            $statement->setKeyPairs(json_decode($pairingConfig->getValue()));
        }
        
    }
    
    private function validateStatement($statement) {        
        // 1. Save keyword to config
        // ------------------------------------------------------------------------------
        $pairingControlCode = HeaderKey::STATEMENT_PAIRING_HISTORY.'_'.strtoupper($statement->getUnit()->getId());
        $pairingConfig = $this->entityManager
                    ->getRepository('MorusFasBundle:FasConfig')
                    ->findOneByControlCode($pairingControlCode);
        
        if (!$pairingConfig) {
            $pairingConfig = new FasConfig();
        }
        $pairingConfig->setControlCode(HeaderKey::STATEMENT_PAIRING_HISTORY.'_'.strtoupper($statement->getUnit()->getId()));
        $pairingConfig->setValue(json_encode($statement->getKeyPairs()));
        $this->entityManager->persist($pairingConfig);
        $this->entityManager->flush();
        
        // 2. Validate statement content 
        // ------------------------------------------------------------------------------
        $this->totalProcessedCount = 0;
        $datetimeFormat = $statement->getDatetimeFormat();
        $dateFormat = $statement->getDateFormat();
        $timeFormat = $statement->getTimeFormat();
        $this->sample = array();
        foreach ($this->reader as $rowNum => $row) {   
            // Get Sample
            if ($rowNum = 1) {
                $this->sample['CardNumber'] = trim($row[$statement->getCardNumberHeader()]);
                $this->sample['LicenceNumber'] = trim($row[$statement->getLicenceNumberHeader()]);
                $this->sample['Site'] = trim($row[$statement->getSiteHeader()]);
                $this->sample['ReceiptNumber'] = trim($row[$statement->getReceiptNumberHeader()]);
                if ($statement->getSplitDateTime()) {
                    $this->sample['TransactionDate'] = trim($row[$statement->getTransactionDateHeader()]);
                    $this->sample['TransactionTime'] = trim($row[$statement->getTransactionTimeHeader()]);
                } else { 
                   $this->sample['TransactionDatetime'] = trim($row[$statement->getTransactionDatetimeHeader()]);
                }
                $this->sample['ProductName'] = trim($row[$statement->getProductNameHeader()]);
                $this->sample['UnitDiscount'] = trim($row[$statement->getUnitDiscountHeader()]);
                $this->sample['Volume'] = trim($row[$statement->getVolumeHeader()]);
                $this->sample['UnitPrice'] = trim($row[$statement->getUnitPriceHeader()]);
                $this->sample['NetAmount'] = trim($row[$statement->getNetAmountHeader()]);
            }
            
           $this->totalProcessedCount = $this->totalProcessedCount + 1;
           $nullCard = false; $nullLic = false; $nullSite = false; 
           
           // Validate Columns and write to logs
           $nullCard = ($this->isNullOrEmpty(trim($row[$statement->getCardNumberHeader()])) ? true : false );
           $nullLic = ($this->isNullOrEmpty(trim($row[$statement->getLicenceNumberHeader()])) ? true : false );
           $nullSite = ($this->isNullOrEmpty(trim($row[$statement->getSiteHeader()])) ? true : false );
           $nullRpt = ($this->isNullOrEmpty(trim($row[$statement->getReceiptNumberHeader()])) ? true : false );
           
           if ($statement->getSplitDateTime()) {
               $nullTranDate = ($this->isNullOrEmpty(trim($row[$statement->getTransactionDateHeader()])) ? true : false );
                $nullTranTime = ($this->isNullOrEmpty(trim($row[$statement->getTransactionTimeHeader()])) ? true : false );
                $nullTranDateTime = false;
                
                // Check if datetime is valid
                $date = trim($row[$statement->getTransactionDateHeader()]);
                $time = trim($row[$statement->getTransactionTimeHeader()]);
                $transDate = \DateTime::createFromFormat($dateFormat, $date);
                $transTime = \DateTime::createFromFormat($timeFormat, $time);
                
                $invalidTranDate = (!$transDate ? true : false);
                $invalidTranTime = (!$transTime ? true : false);
                $invalidTranDateTime = false;
           } else { 
                $nullTranDate = false;
                $nullTranTime = false;
                
                $nullTranDateTime = ($this->isNullOrEmpty(trim($row[$statement->getTransactionDatetimeHeader()])) ? true : false );
                
                // Check if datetime is valid
                $dateTime = trim($row[$statement->getTransactionDatetimeHeader()]);
                $transDatetime = \DateTime::createFromFormat($datetimeFormat, $dateTime);
                
                $invalidTranDate = false;
                $invalidTranTime = false;
                $invalidTranDateTime = (!$transDatetime ? true : false);
           }
           $nullPdtName = ($this->isNullOrEmpty(trim($row[$statement->getProductNameHeader()])) ? true : false );
           $nullUnitDis = ($this->isNullOrEmpty(trim($row[$statement->getUnitDiscountHeader()])) ? true : false );
           $nullVolume = ($this->isNullOrEmpty(trim($row[$statement->getVolumeHeader()])) ? true : false );
           $nullPx = ($this->isNullOrEmpty(trim($row[$statement->getUnitPriceHeader()])) ? true : false );
           $nullAmt = ($this->isNullOrEmpty(trim($row[$statement->getNetAmountHeader()])) ? true : false );
           
           if ($nullCard || $nullLic || $nullSite || $nullRpt || $nullTranDateTime || $nullTranDate || $nullTranTime
                        || $nullPdtName || $nullUnitDis || $nullVolume || $nullPx || $nullAmt || $invalidTranDateTime || $invalidTranDate || $invalidTranTime ) {
               $importLog = new ImportLog($rowNum);
               
               $importLog->setLog($nullCard, $nullLic, $nullSite, $nullRpt, $nullTranDateTime, $nullTranDate, $nullTranTime
                       , $nullPdtName, $nullUnitDis, $nullVolume, $nullPx, $nullAmt, $invalidTranDateTime, $invalidTranDate, $invalidTranTime );
               array_push($this->errorLogs, $importLog);
               $this->hasError = true;
           }
           
           asort($this->errorLogs);
        }
        
        
        
    }
    
    private function isNullOrEmpty($value) {
        return (!isset($value) || trim($value)==='');
    }
    
    public function getName() {
        return 'statement_import_flow';
    }
}

