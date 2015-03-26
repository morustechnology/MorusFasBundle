<?php

namespace Morus\FasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Morus\FasBundle\Entity\Export;
use Morus\FasBundle\Entity\Statement;
use Morus\FasBundle\Entity\Product;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Reader\ExcelReader;
use Ddeboer\DataImport\Workflow;
use SplFileObject;


/**
 * Statement controller.
 *
 */
class StatementController extends Controller
{    
    /**
     * Lists all Statement entities.
     *
     */
    public function indexAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MorusFasBundle:Statement')->findAll();

        $form = $this->createForm('fas_statement_list', $entities, array(
            'attr'   => array('id' => 'fas_stmt_list_frm'),
            'action' => $this->generateUrl('morus_fas_statement'),
            'method' => 'POST',
        ));

        $form->add('export_invoice', 'submit', array('label' => 'export', 'attr' => array( 'style' => 'display:none' )));
        $form->add('delete_invoice', 'submit', array('label' => 'delete', 'attr' => array( 'style' => 'display:none' )));
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            if ($form->get('export_invoice')->isClicked()) {
                $expStmtsId = array();
                $expStmtsData = $form->getData();
                $expStmts = $expStmtsData['id'];
                foreach ($expStmts as $expStmt) {
                    $expStmtsId[] = $expStmt->getId();
                }
                $session = $this->get('session');
                $session->set('expStmts', $expStmtsId);
                
                return $this->redirect($this->generateUrl('morus_fas_statement_export'));
            } elseif ($form->get('delete_invoice')->isClicked()) {
                $delStmtsData = $form->getData();
                $delStmts = $delStmtsData['id'];
                $session = $this->get('session');
                $session->set('delStmts', $delStmts);
                return $this->redirect($this->generateUrl('morus_fas_statement_delete'));
            }
        }

        
        return $this->render('MorusFasBundle:Statement:index.html.twig', array(
            'entities' => $entities,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * Handle Ajax call for creating new product
     *
     */
    public function createProductAjaxAction(Request $request) {        
        try {
            
            $index = $request->get('index');
            $data = $request->get('fas_product');

            $product = new Product();
            $product->setItemcode($data['itemcode']);
            $product->setItemname($data['itemname']);
            
            $product->setOthername($data['othername']);
            
            if (array_key_exists('useOthername',$data)) {
                $product->setUseOthername($data['useOthername']);
            } else {
                $product->setUseOthername(false);
            }
            
            $product->setUnit('L');
            $product->setDefaultDiscount($data['defaultDiscount']);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            
            $response = array("success" => true);
        } catch (Exception $ex) {
            $response = array("success" => false);
        }
                
        return new Response(json_encode($response)); 

    }
    
    /**
     * Get Customer List id : name
     * 
     */
    public function customerListAction(Request $request) {
        // ----------------------------------------------------
        // Customer List for combo box
        // ----------------------------------------------------
        $qb = $this->getDoctrine()
            ->getManager()
            ->getRepository('MorusFasBundle:Unit')
            ->createQueryBuilder('u');
        $query = $qb
                ->leftJoin('u.unitClasses', 'uc')
                ->where($qb->expr()->eq('uc.controlCode', ':controlCode'))
                ->setParameter('controlCode', 'CUSTOMER');

        $units = $query->getQuery()->getResult();
        
        //return new Response($jsonUnits);
        $unitArray = array();
        foreach($units as $unit){
            $unitArray[$unit->getId()] = $unit->getName();
        }
        $response = array("success" => true, 
            "units" => $unitArray);
        
        return new Response(json_encode($response));
    }
    
    /**
     * Lists Unit entities.
     *
     */
    public function unitVehicleListAction(Request $request)
    {
        $registrationNumbers = $request->get('registration_numbers');
        $qb = $this->getDoctrine()
                ->getManager()
                ->getRepository('MorusFasBundle:Unit')
                ->createQueryBuilder('u');
            
        $query = $qb
                ->addSelect('v')
                ->join('u.vehicles', 'v')
                ->where($qb->expr()->in('v.registrationNumber', $registrationNumbers));
        
        $units = $query->getQuery()->getResult();
        
        return $this->render('MorusFasBundle:Statement:export.customer.vehicle.list.html.twig', array(
            'units' => $units,
        ));
    }
    
    /**
     * Lists product entities.
     *
     */
    public function productListAction(Request $request)
    {
        $itemnames = $request->get('product');
        $qb = $this->getDoctrine()
                ->getManager()
                ->getRepository('MorusFasBundle:Product')
                ->createQueryBuilder('p');
            
        $query = $qb
                ->where($qb->expr()->in('p.itemname', $itemnames));
        
        $product = $query->getQuery()->getResult();
        
        return $this->render('MorusFasBundle:Statement:export.product.list.html.twig', array(
            'product' => $product,
        ));
    }
    
    /**
     * Export Statement to invoice
     *
     */
    public function exportAction(Request $request)
    {  
        // Get selected statement for export
        $session = $this->get('session');
        if ($session && $session->get('expStmts')) {
            $export = new Export();
            
            $expStmts = $session->get('expStmts');
            
            // Get statements to be exported
            $sqb = $this->getDoctrine()->getManager()
                    ->getRepository('MorusFasBundle:Statement')
                    ->createQueryBuilder('s');
            $uQuery = $sqb
                    ->where($sqb->expr()->in('s.id', $expStmts));

            $statements = $uQuery->getQuery()->getResult();
        
            
            foreach($statements as $statement) {
                $export->addStatement($statement);
            }
            
            
            // ----------------------------------------------------
            // Export Form Flow
            // ----------------------------------------------------
            $flow = $this->get('morus_fas.form.flow.invoice.export');
            $flow->bind($export);
            
            // form of the current step
            $export_form = $flow->createForm();
            
            if ($flow->isValid($export_form)) {
                $flow->saveCurrentStepData($export_form);

                if ($flow->nextStep()) {
                    // form for the next step
                    $export_form = $flow->createForm();
                } else {
                    // flow finished
                    $em = $this->getDoctrine()->getManager();
                    $em->getConnection()->beginTransaction();
                    try {
                        
                        $aem = $this->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service
                        
                        // Generate and save next invoice number
                        $config = $aem->getAcceticConfigRepository()->findOneByControlCode('INV_NEXT_NUM');

                        if($config) {
                            
                            $suff = $aem->getInvSuff($flow->nextInvoiceNumber);
                            $config->setValue($suff);
                            $em->persist($config);
                        }
                
                        
                        // Add generated ar to export
                        foreach($flow->ars as $ar){
                            $export->addAr($ar);
                            $em->persist($ar);
                        }
                        
                        // Update statement status
                        $complete = $em->getRepository('MorusFasBundle:StatementStatus')
                                ->findOneByControlCode('COMPLETE');
                        
                        foreach($export->getStatements() as $stmt) {
                            $stmt->setStatementStatus($complete);
                            $em->persist($stmt);                            
                        }
                        
                        $em->persist($export);
                        $em->flush();
                        $flow->reset(); // remove step data from the session
                        
                        // Try and commit the transaction
                        $em->getConnection()->commit();
                    } catch (Exception $ex) {
                        // Rollback the failed transaction attempt
                        $em->getConnection()->rollback();
                        throw $ex;
                    }
                    
                    return $this->redirect($this->generateUrl('morus_fas_ar')); // redirect when done
                }
            }

            // ----------------------------------------------------
            // New Product Form
            // ----------------------------------------------------
            $product = new Product();
            $product_form = $this->createForm('fas_product', $product, array(
                'attr' => array('id' => 'accetic_product_list'),
                'action' => $this->generateUrl('morus_fas_statement_export_create_product_ajax'),
                'method' => 'POST',
            ));
            
            $product_form->add('submit', 'submit', array(
                    'label' => $this->get('translator')->trans('btn.save'),
                    'attr' => array('style' => 'display:none')
                ));
            
            return $this->render('MorusFasBundle:Statement:export.html.twig', array(
                'export_form' => $export_form->createView(),
                'product_form' => $product_form->createView(),
                'flow' => $flow,
            ));
        } else {
            return $this->redirect($this->generateUrl('morus_fas_statement'));
        }
        
    }
    
    /**
     * Import new Statement
     *
     */
    public function importAction(Request $request)
    {      
        $em = $this->getDoctrine()->getManager();
        
        $init = $em->getRepository('MorusFasBundle:StatementStatus')
                ->findOneByControlCode('NEW');
        
        // Initialize new statement
        $statement = new Statement();
        $statement->setStatementStatus($init);
        
        $flow = $this->get('morus_fas.form.flow.statement.import'); // must match the flow's service id
        $flow->bind($statement);
        
        // form of the current step
        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                // form for the next step
                $form = $flow->createForm();
            } else {
                // flow finished
                $em = $this->getDoctrine()->getManager();
                $em->persist($statement);
                $em->flush();

                $flow->reset(); // remove step data from the session

                return $this->redirect($this->generateUrl('morus_fas_statement'));
            }
        }

        return $this->render('MorusFasBundle:Statement:import.html.twig', array(
            'form' => $form->createView(),
            'flow' => $flow,
        ));
    }
    
    /**
     * Deletes a Statement entity.
     *
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        // Get selected statement for export
        $session = $this->get('session');
        if ($session && $session->get('delStmts')) {
            $delStmts = $session->get('delStmts');
            foreach($delStmts as $delStmt) {
                $statement = $em->getRepository('MorusFasBundle:Statement')
                    ->findOneById($delStmt->getId());
                $em->remove($statement);
            }
        }
        
        $em->flush();

        return $this->redirect($this->generateUrl('morus_fas_statement'));
    }
}
