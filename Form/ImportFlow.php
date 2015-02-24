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
    public $cardNumberLogs = array();
    public $licenceNumberLogs = array();
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
        
        foreach ($this->reader as $rowNum => $row) {           
           // Validate Card Number Column
           if ($this->isNullOrEmpty($row[$statement->getCardNumberHeader()])) {
                $this->cardNumberLogs[] = $this->container->get('morus_fas.classes.importlog')
                        ->setLog($rowNum, $statement->getCardNumberHeader(), ImportLog::EMPTY_NULL_VALUE);
           }
           
           // Validate Licence Number Column
           if ($this->isNullOrEmpty($row[$statement->getLicenceNumberHeader()])) {
                $this->licenceNumberLogs[] = $this->container->get('morus_fas.classes.importlog')
                        ->setLog($rowNum, $statement->getLicenceNumberHeader(), ImportLog::EMPTY_NULL_VALUE);
           }
           
           $productName = $row[$statement->getProductNameHeader()];
           $p = $this->entityManager
                   ->getRepository('MorusAcceticBundle:Parts')
                   ->findOneByItemname($productName);
           
           if (!$p && !in_array($productName,$this->newProductList)) {
               $this->newProductList[] = $productName;
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

