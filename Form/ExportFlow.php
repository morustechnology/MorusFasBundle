<?php

namespace Morus\FasBundle\Form;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Craue\FormFlowBundle\Form\FormFlow;
use Ddeboer\DataImport\Reader\CsvReader;
use SplFileObject;



class ExportFlow extends FormFlow {
    
    protected $revalidatePreviousSteps = false;
    
    public $units = array();
    public $parts = array();
    
    public $stmtList = array();
    public $stmtRowCnt = 0;
    
    public $stmtProd = array();
    public $stmtVec = array();
    
    
    
    public $existProdList = array();
    public $newProdList = array();
    
    public $existVecList = array();
    public $newVecList = array();
    
    public function __construct(EntityManager $entityManager, Container $container) {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }
    
    protected function loadStepsConfig() {
        return array(
            array(
                'label' => 'export_step.analysis_summary',
            ),
            array(
                'label' => 'export_step.product_search',
            ),
            array(
                'label' => 'export_step.vehicle_search',
            ),
            array(
                'label' => 'export_step.confirm_export',
            ),
        );
    }
    
    public function getFormOptions($step, array $options = array()) {
        $options = parent::getFormOptions($step, $options);
        
        
        switch($step) {
            case 1:
                $this->analyzeStatement();
                break;
            case 2:
                $this->queryProduct($this->stmtProd);
                break;
            case 3:
                $this->queryVehicle($this->stmtVec);
                break;
            case 4:
                $this->fasPL();
                break;
        }
        
        return $options;
    }
    
    /**
     * Generate FAS Profit and Loss Summary
     */
    private function fasPL() {
        // Get Product which appear in statements
        $pqb = $this->entityManager
                ->getRepository('MorusFasBundle:Parts')
                ->createQueryBuilder('p');
            
        $pQuery = $pqb
                ->where($pqb->expr()->in('p.itemname', $this->stmtProd));
        
        $this->parts = $pQuery->getQuery()->getResult(); 
        
        // Get All unit who has vehicle appear in statements
        $uqb = $this->entityManager
                ->getRepository('MorusFasBundle:Unit')
                ->createQueryBuilder('u');
            
        $uQuery = $uqb
                ->join('u.vehicles', 'v')
                ->where($uqb->expr()->in('v.registrationNumber', $this->stmtVec));
        
        $this->units = $uQuery->getQuery()->getResult();
        
        // 1. Process Statment, create Invoice for each row.
        $stmts = $this->getFormData()->getStatements();
        $export = $this->getFormData();
        foreach( $stmts as $stmt) {
            $file = new SplFileObject($stmt->getWebPath());
            $reader = new CsvReader($file);
            $reader->setHeaderRowNumber(0);
            
            foreach ($reader as $rowNum => $row) {
                $invoice = new \Morus\FasBundle\Entity\Invoice();
                $invoice->setCardNumber($row[$stmt->getCardNumberHeader()]);
                
                $invoice->setSite($row[$stmt->getSiteHeader()]);
                $invoice->setReceiptNumber($row[$stmt->getReceiptNumberHeader()]);
                $invoice->setQty($row[$stmt->getVolumeHeader()]);
                $invoice->setCost($row[$stmt->getNetAmountHeader()]);
                $invoice->setSellprice($row[$stmt->getUnitPriceHeader()]);
                //$invoice->setDescription($row[$stmt->getLicenceNumberHeader()]);
                if ($stmt->getSplitDateTime() == true) {
                    // Convert Date Fomat
                    $date = $row[$stmt->getTransactionDateHeader()];
                    $dateFormat = $stmt->getDateFormat();
                    $transDate = \DateTime::createFromFormat($dateFormat, $date);
                    $invoice->setTransDate($transDate);
                    
                    $time = $row[$stmt->getTransactionTimeHeader()];
                    $timeFormat = $stmt->getTimeFormat();
                    $transTime = \DateTime::createFromFormat($timeFormat, $time);
                    $invoice->setTransTime($transTime);
                } else {
                    $datetimeFormat = $stmt->getDatetimeFormat();
                    $dateTime = $row[$stmt->getTransactionDatetimeHeader()];
                    $transDatetime = \DateTime::createFromFormat($datetimeFormat, $dateTime);
                    $invoice->setTransDate($transDatetime->format('Y-m-d'));
                    $invoice->setTransTime($transDatetime->format('H:i:s'));
                    
                }
                
                // 2. Search Units with the same vehicle number
                $registrationNumber = $row[$stmt->getLicenceNumberHeader()];
                
                
                foreach ($this->units as $unit ) {
                    $flag = false;
                    foreach ($unit->getVehicles() as $vehicle) {
                        if ($vehicle->getRegistrationNumber() == $registrationNumber) {
                            $vehicle->addInvoice($invoice);
                            $invoice->setVehicle($vehicle);
                            $flag = true;
                        }
                        if ($flag == true) break;
                    }
                    if ($flag == true) break;
                }
                
                //3. Search Product and add to invoice
                $itemname = $row[$stmt->getProductNameHeader()];
                foreach ($this->parts as $parts ) {
                    $flag = false;
                    if ($parts->getItemname() == $itemname) {
                        $invoice->setParts($parts);
                        $parts->addInvoice($invoice);
                        $invoice->setDescription($parts->getItemName());
                        $flag = true;
                    }
                    if ($flag == true) break;
                }
            }
        } // End process statement
        
        // Create Transaction and AR then add to this export
        foreach( $this->units as $unit) {
            // One transaction + ar per custome
            $transaction = new \Morus\FasBundle\Entity\Transaction();
            $ar = new \Morus\FasBundle\Entity\Ar();
            
            // Set relationship
            $transaction->setAr($ar);
            $transaction->setUnit($unit);
            $ar->setTransaction($transaction);
            
            // Set Invoice Number
            $invPrefix = $this->entityManager
                    ->getRepository('MorusFasBundle:AcceticConfig')
                    ->findOneByControlCode('INV_PREFIX');
            $invNextNumber = $this->entityManager
                    ->getRepository('MorusFasBundle:AcceticConfig')
                    ->findOneByControlCode('INV_NEXT_NUM');
            
            $num = $invNextNumber->getValue();
            if ($invPrefix && $invNextNumber) {
                $ar->setInvnumber($invPrefix->getValue() . $num);
            }
            
            $invNextNumber->setValue($num + 1);
            $this->entityManager->persist($invNextNumber);
            $this->entityManager->flush();
            
            // Add invoice line to transaction.
            foreach ($unit->getVehicles() as $vehicle) {
                foreach ($vehicle->getInvoices() as $invoice) {
                    $transaction->addInvoice($invoice);
                }
            }
            $export->addTransaction($transaction);
        }
        
    }
    
    /**
     * Search for Product and Vehicle in statement
     */
    private function analyzeStatement() {
        $this->stmtVec = array();
        $this->stmtProd = array();
        
        $stmts = $this->getFormData()->getStatements();
        foreach( $stmts as $stmt) {
            $file = new SplFileObject($stmt->getWebPath());
            $reader = new CsvReader($file);
            $reader->setHeaderRowNumber(0);
            $rowCnt = 0;
            foreach ($reader as $rowNum => $row) {
                $this->stmtRowCnt = $this->stmtRowCnt + 1;
                
                // Find Unique Product Name in statement
                $pName = $row[$stmt->getProductNameHeader()];
                if (!in_array($pName, $this->stmtProd)) {
                    $pCode = strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $pName)));
                    $this->stmtProd[$pCode] = $pName;
                }

                // Find Unique License number in statement
                $vNum = $row[$stmt->getLicenceNumberHeader()];
                if (!in_array($vNum, $this->stmtVec)) {
                    $vCode = strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $vNum)));
                    $this->stmtVec[$vCode] = $vNum;
                }
            }
        }
        
        asort($this->stmtVec);
        asort($this->stmtProd);
    }
    
    /**
     * Query DB for existing product
     */
    private function queryProduct($prodList) {
        $this->newProdList = array();
        $this->existProdList = array();
        
        // Search DB for matching product
        foreach( $prodList as $key => $value) {
            $p = $this->entityManager
                ->getRepository('MorusFasBundle:Parts')
                ->findOneByItemname($value);

            $p ? $this->existProdList[] = $p : $this->newProdList[$key] = $value;
        }

    }
    
    /**
     * Query DB for existing vehicle
     */
    private function queryVehicle($vecList) {
        $this->newVecList = array();
        $this->existVecList = array();
        
        // Search DB for matching vehicle
        foreach( $vecList as $key => $value) {
            $v = $this->entityManager
                ->getRepository('MorusFasBundle:Vehicle')
                ->findOneByRegistrationNumber($value);

            $v ? $this->existVecList[] = $v : $this->newVecList[$key] = $value;
        }
    }
    
    public function getName() {
        return 'fas_export_flow';
    }
}

