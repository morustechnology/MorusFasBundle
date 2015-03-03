<?php

namespace Morus\FasBundle\Form;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Craue\FormFlowBundle\Form\FormFlow;
use Ddeboer\DataImport\Reader\CsvReader;
use SplFileObject;



class ExportFlow extends FormFlow {
    
    
    public $prodList = array();
    public $newProdList = array();
    
    public $vceList = array();
    public $newVceList = array();
    
    public function __construct(EntityManager $entityManager, Container $container) {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }
    
    protected function loadStepsConfig() {
        return array(
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
                
                break;
            case 4:
                break;
        }
        
        return $options;
    }
    
    /**
     * Search for New Product
     * @param type $export
     */
    private function analyzeStatement() {
        // Init Arrays
        $this->vceList = array();
        $this->newVceList = array();
        $this->newProdList = array();
        $this->prodList = array();
        
        
        $stmts = $this->getFormData()->getStatements();
        foreach( $stmts as $stmt) {
            
            $file = new SplFileObject($stmt->getWebPath());
            $reader = new CsvReader($file);
            $reader->setHeaderRowNumber(0);
            
            foreach ($reader as $rowNum => $row) {
                // Find Unique Product Name in statement
                $pName = $row[$stmt->getProductNameHeader()];
                if (!in_array($pName, $this->newProdList)) {
                    $pCode = strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $pName)));
                    $this->newProdList[$pCode] = $pName;
                }
                
                // Find Unique License number in statement
                $vNum = $row[$stmt->getLicenceNumberHeader()];
                if (!in_array($vNum, $this->newVceList)) {
                    $vCode = strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $vNum)));
                    $this->newVceList[$vCode] = $vNum;
                }
            }
            
            // Search DB for matching product
            foreach( $this->newProdList as $key => $value) {
                $dbpe = $this->entityManager
                    ->getRepository('MorusFasBundle:Parts')
                    ->findOneByItemname($value);
                
                $dbpe ? $this->prodList[] = $dbpe : null;
                if ($dbpe) {
                    unset($this->newProdList[$key]);
                }
            }
            
            // Search DB for matching vehicle
            foreach( $this->newVceList as $key => $value) {
                $v = $this->entityManager
                    ->getRepository('MorusFasBundle:Vehicle')
                    ->findOneByRegistrationNumber($value);
                
                $v ? $this->vceList[] = $v : null;
                if ($v) {
                    unset($this->newVceList[$key]);
                }
            }
            
        }
        
        // sort array
        asort($this->newVceList);
        asort($this->newProdList);
    }
    
    
    
    public function getName() {
        return 'fas_export_flow';
    }
}

