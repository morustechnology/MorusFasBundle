<?php

namespace Morus\FasBundle\Form;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Craue\FormFlowBundle\Form\FormFlow;
use Ddeboer\DataImport\Reader\CsvReader;
use SplFileObject;



class ExportFlow extends FormFlow {
    
    protected $revalidatePreviousSteps = false;
    
    public $stmtList = array();
    public $stmtRowCnt = 0;
    
    public $stmtProdList = array();
    public $stmtVecList = array();
    
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
                $this->queryProduct($this->stmtProdList);
                break;
            case 3:
                $this->queryVehicle($this->stmtVecList);
                break;
        }
        
        return $options;
    }
    
    /**
     * Search for Product and Vehicle in statement
     */
    private function analyzeStatement() {
        $this->stmtVecList = array();
        $this->stmtProdList = array();
        
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
                if (!in_array($pName, $this->stmtProdList)) {
                    $pCode = strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $pName)));
                    $this->stmtProdList[$pCode] = $pName;
                }

                // Find Unique License number in statement
                $vNum = $row[$stmt->getLicenceNumberHeader()];
                if (!in_array($vNum, $this->stmtVecList)) {
                    $vCode = strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $vNum)));
                    $this->stmtVecList[$vCode] = $vNum;
                }
            }
        }
        
        asort($this->stmtVecList);
        asort($this->stmtProdList);
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

