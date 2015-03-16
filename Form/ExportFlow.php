<?php

namespace Morus\FasBundle\Form;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Craue\FormFlowBundle\Form\FormFlow;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Reader\ExcelReader;
use Ddeboer\DataImport\Workflow;
use SplFileObject;



class ExportFlow extends FormFlow {
    
    protected $revalidatePreviousSteps = false;
    
    public $nextInvoiceNumber;
    public $ars = array(); // Ar for summary step
    
    public $stmtList = array(); // Statements to be exported
    public $stmtRowCnt = 0;     // Total records to be exported
    
    public $stmtProd = array();  // Store products which appear in statement(s)
    public $stmtVec = array();   // Store vechicle licence number only appear in statement(s)
    
    public $newProdList = array();  // Store new products
    public $newVecList = array();   // Store new vechicles
    
    public function __construct(EntityManager $entityManager, Container $container) {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }
    
    protected function loadStepsConfig() {
        return array(
            array(
                'label' => 'export_step.analysis_summary',
                'type' => new Type\ExportStep1Type(),
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
        
        $this->step = $step;
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
     * Search for Product and Vehicle in statement
     */
    private function analyzeStatement() {
        $this->stmtVec = array();
        $this->stmtProd = array();
        
        $stmts = $this->getFormData()->getStatements();
        foreach( $stmts as $stmt) {
            $file = new SplFileObject($stmt->getWebPath());
            $mineType = mime_content_type($file->getFileInfo()->getPathname());
        
            if ($mineType == 'application/vnd.ms-excel') {
                $reader = new ExcelReader($file);
            } else {
                $reader = new CsvReader($file);
            }
            
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
        
        // Search DB for matching product
        foreach( $prodList as $key => $value) {
            $p = $this->entityManager
                ->getRepository('MorusFasBundle:Parts')
                ->findOneByItemname($value);

            $p ? null : $this->newProdList[$key] = $value;
        }

    }
    
    /**
     * Query DB for existing vehicle
     */
    private function queryVehicle($vecList) {
        $this->newVecList = array();
        
        // Search DB for matching vehicle
        foreach( $vecList as $key => $value) {
            $v = $this->entityManager
                ->getRepository('MorusFasBundle:Vehicle')
                ->findOneByRegistrationNumber($value);

            $v ? null : $this->newVecList[$key] = $value;
        }
    }
    
    /**
     * Generate FAS Profit and Loss Summary
     */
    private function fasPL() {
        $this->ars = array();
        
        $aem = $this->container->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service
        $this->nextInvoiceNumber = $aem->nextInvNum();
                
        // Init invoice date and default due date
        $dueinterval = $aem->getAcceticConfigRepository()->findOneByControlCode('INV_DUE_INTERVAL')->getValue();
        $invdate = new \DateTime('now');
        $duedate = new \DateTime('now');
        $duedate->add(new \DateInterval('P'.$dueinterval.'D'));
        
        // Get All unit who has vehicle appear in statements
        $uqb = $this->entityManager
                ->getRepository('MorusFasBundle:Unit')
                ->createQueryBuilder('u');
        $uQuery = $uqb
                ->addSelect('v')
                ->join('u.vehicles', 'v')
                ->where($uqb->expr()->in('v.registrationNumber', $this->stmtVec));
        
        $units = $uQuery->getQuery()->getResult();
        
        // Get data
        $stmts = $this->getFormData()->getStatements();
        $export = $this->getFormData();
        $ignoreKeywords = explode(',', str_replace(' ', '', $export->getIgnoreKeywords()));
        
        // 1. Create Transaction and AR then add to this export
        foreach( $units as $unit) {
            // One transaction + ar per custome
            $ar = new \Morus\FasBundle\Entity\Ar();
            $transaction = new \Morus\FasBundle\Entity\Transaction();
            $this->ars[] = $ar;
            
            
            // Set ar and transaction relationship
            $ar->setTransaction($transaction);
            $transaction->setAr($ar);
            
            // Set ar and unit relationship
            $ar->setUnit($unit);
            $unit->addAr($ar);
            
            // ar detail
            $ar->setInvnumber($this->nextInvoiceNumber);
            $this->nextInvoiceNumber = $aem->incInvNum($this->nextInvoiceNumber, 1);
            $ar->setTransdate($invdate);
            $ar->setDuedate($duedate);
        }
        
        foreach( $stmts as $stmt) {
            // Open file to read each row
            $file = new SplFileObject($stmt->getWebPath());
            $mineType = mime_content_type($file->getFileInfo()->getPathname());
        
            if ($mineType == 'application/vnd.ms-excel') {
                $reader = new ExcelReader($file);
            } else {
                $reader = new CsvReader($file);
            }
            
            $reader->setHeaderRowNumber(0);
            
            foreach ($reader as $rowNum => $row) {
                $site = $row[$stmt->getSiteHeader()];
                $registrationNumber = $row[$stmt->getLicenceNumberHeader()];
                $productName = $row[$stmt->getProductNameHeader()]; 
                
                
                // 1. Process Statment, create Invoice for each row.
                $invoice = new \Morus\FasBundle\Entity\Invoice();
                $invoice->setCardNumber($row[$stmt->getCardNumberHeader()]);
                $invoice->setSite($row[$stmt->getSiteHeader()]);
                $invoice->setReceiptNumber($row[$stmt->getReceiptNumberHeader()]);
                $invoice->setQty($row[$stmt->getVolumeHeader()]);
                
                $invoice->setNetamount($row[$stmt->getNetAmountHeader()]);
                $invoice->setLicence($row[$stmt->getLicenceNumberHeader()]);
                $unitPrice = $row[$stmt->getUnitPriceHeader()];
                $unitDiscount = abs($row[$stmt->getUnitDiscountHeader()]);
                $sellPx = $unitPrice + $unitDiscount; // Calculate sell price
                $invoice->setUnitprice($unitPrice);
                $invoice->setUnitDiscount($unitDiscount);
                $invoice->setSellprice($sellPx);  
                
                
                if ($stmt->getSplitDateTime() == true) { // Set transaction date time
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
                
                // Check if gas station is discount exclusive
                $ignore = false;
                foreach( $ignoreKeywords as $keywords) {
                    if (strpos($site, $keywords) !== false) {
                        $invoice->setSelldiscount(0);
                        $invoice->setcustomerdiscount(false);
                        $ignore = true;
                        break;
                    }
                }
                
                // search unit with registration number
                $qb = $this->entityManager
                        ->getRepository('MorusFasBundle:Unit')
                        ->createQueryBuilder('u');
                $query = $qb
                        ->join('u.vehicles', 'v', 'WITH', 'v.registrationNumber = :registrationNumber')
                        ->setParameter('registrationNumber', $registrationNumber);

                $sunit = $query->getQuery()->getSingleResult();
                
                foreach ($this->ars as $ar) {
                    if ($ar->getUnit() == $sunit) {
                        $ar->getTransaction()->addInvoice($invoice);
                        $invoice->setTransaction($ar->getTransaction());
                    }
                }
                
                // 2. Search Units with the same vehicle number / also check for discount
                if (!$ignore) {
                    $this->getUnitPartsDiscount($sunit, $invoice, $productName);
                }

            }
        } // End process statement
    }
    
    /**
     * 
     * @param type $unitParts
     * @param type $invoice
     * 
     * Search for customer product discount, use product default discount if not found
     */
    private function getUnitPartsDiscount($unit, $invoice, $productName) {
        // Get Product which appear in statements
        $pqb = $this->entityManager
                ->getRepository('MorusFasBundle:Parts')
                ->createQueryBuilder('p');
        $pQuery = $pqb
                ->where($pqb->expr()->in('p.itemname', $this->stmtProd));
        
        $parts = $pQuery->getQuery()->getResult(); 
        
        foreach ($parts as $p ) {
            if ($p->getItemname() == $productName) {
                $invoice->setParts($p);
                
                if($p->getUseOthername()) {
                    $partsName = $p->getOthername();
                } else {
                    $partsName = $p->getItemName();
                }
                    
                $invoice->setDescription($partsName);
                
                // Get All unit who has vehicle appear in statements
                $qb = $this->entityManager
                        ->getRepository('MorusFasBundle:UnitParts')
                        ->createQueryBuilder('up');
                $query = $qb
                        ->join('up.parts', 'p', 'WITH', 'p = :parts')
                        ->join('up.unit', 'u', 'WITH', 'u = :unit')
                        ->setParameter('parts', $p)
                        ->setParameter('unit', $unit);

                $unitParts = $query->getQuery()->getResult();
                
                
                if ($unitParts) {
                    
                    $discount = $unitParts[0]->getDiscount();
                    $invoice->setSelldiscount($discount);
                    $invoice->setcustomerdiscount(true);
                } else {
                    $discount = $p->getDefaultDiscount();
                    $invoice->setSelldiscount($discount);
                    $invoice->setcustomerdiscount(false);
                    
                }
            }
        }
    }
    
    public function getName() {
        return 'fas_export_flow';
    }
}

