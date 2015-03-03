<?php

namespace Morus\FasBundle\Form;


use Craue\FormFlowBundle\Form\FormFlow;
use Ddeboer\DataImport\Reader\CsvReader;
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
    private $container;
    protected $reader;
    
    public $isSplitDateTime = false;
    public $result;
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
        $this->reader = new CsvReader($file);
        $this->reader->setHeaderRowNumber(0);
        $headerAry = array();
        foreach($this->reader->getColumnHeaders() as $h) {
            $headerAry[$h] = $h;
        }
        $statement->setHeaders($headerAry);
        
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
        foreach ($this->reader as $rowNum => $row) {    
           $this->totalProcessedCount = $this->totalProcessedCount + 1;
           $nullCard = false; $nullLic = false; $nullSite = false; 
           
           // Validate Columns and write to logs
           $nullCard = ($this->isNullOrEmpty($row[$statement->getCardNumberHeader()]) ? true : false );
           $nullLic = ($this->isNullOrEmpty($row[$statement->getLicenceNumberHeader()]) ? true : false );
           $nullSite = ($this->isNullOrEmpty($row[$statement->getSiteHeader()]) ? true : false );
           $nullRpt = ($this->isNullOrEmpty($row[$statement->getReceiptNumberHeader()]) ? true : false );
           
           if ($statement->getSplitDateTime()) {
               $nullTranDate = ($this->isNullOrEmpty($row[$statement->getTransactionDateHeader()]) ? true : false );
                $nullTranTime = ($this->isNullOrEmpty($row[$statement->getTransactionTimeHeader()]) ? true : false );
                $nullTranDateTime = false;
           } else { 
                $nullTranDate = false;
                $nullTranTime = false;
                $nullTranDateTime = ($this->isNullOrEmpty($row[$statement->getTransactionDatetimeHeader()]) ? true : false );
           }
           $nullPdtName = ($this->isNullOrEmpty($row[$statement->getProductNameHeader()]) ? true : false );
           $nullPdtCode = ($this->isNullOrEmpty($row[$statement->getProductCodeHeader()]) ? true : false );
           $nullVolume = ($this->isNullOrEmpty($row[$statement->getVolumeHeader()]) ? true : false );
           $nullPx = ($this->isNullOrEmpty($row[$statement->getUnitPriceHeader()]) ? true : false );
           $nullAmt = ($this->isNullOrEmpty($row[$statement->getNetAmountHeader()]) ? true : false );
           
           if ($nullCard || $nullLic || $nullSite || $nullRpt || $nullTranDateTime || $nullTranDate || $nullTranTime
                        || $nullPdtName || $nullPdtCode || $nullVolume || $nullPx || $nullAmt ){
               $importLog = new ImportLog($rowNum);
               
               $importLog->setLog($nullCard, $nullLic, $nullSite, $nullRpt, $nullTranDateTime, $nullTranDate, $nullTranTime
                       , $nullPdtName, $nullPdtCode, $nullVolume, $nullPx, $nullAmt);
               array_unshift($this->errorLogs, $importLog);
               $this->hasError = true;
           }
        }
    }
    
    private function isNullOrEmpty($value) {
        return (!isset($value) || trim($value)==='');
    }
    
    public function getName() {
        return 'statement_import_flow';
    }
}

