<?php

namespace Morus\FasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Morus\FasBundle\Entity\Export;
use Morus\FasBundle\Entity\Statement;
use Morus\FasBundle\Entity\Parts;
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
                $expStmts = $form->getData()['id'];
                foreach ($expStmts as $expStmt) {
                    $expStmtsId[] = $expStmt->getId();
                }
                $session = $this->get('session');
                $session->set('expStmts', $expStmtsId);
                
                return $this->redirect($this->generateUrl('morus_fas_statement_export'));
            } elseif ($form->get('delete_invoice')->isClicked()) {
                $delStmts = $form->getData()['id'];
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
            $data = $request->get('fas_parts');

            $parts = new Parts();
            $parts->setItemcode($data['itemcode']);
            $parts->setItemname($data['itemname']);
            
            $parts->setOthername($data['othername']);
            
            if (array_key_exists('useOthername',$data)) {
                $parts->setUseOthername($data['useOthername']);
            } else {
                $parts->setUseOthername(false);
            }
            
            $parts->setUnit('L');
            $parts->setDefaultDiscount($data['defaultDiscount']);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($parts);
            $em->flush();
            
            $response = array("success" => true);
        } catch (Exception $ex) {
            $response = array("success" => false);
        }
                
        return new Response(json_encode($response)); 

    }
    
    /**
     * Handle Ajax call for creating new unit and add vehicle
     *
     */
    public function customerUpdateAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $aem = $this->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service
            $action = $request->get('ACTION');
            $aUnit = $request->get('fas_unit');
            
            if ($action == 'NEW') {
                $unit = $aem->createUnit('customer');
            } else {
                
                $unit = $em->getRepository('MorusFasBundle:Unit')->find($action);
            }
            
            $addList = array();
            $deleteList = array();
            
            // Get request data
            $aName = $aUnit['name'];
            $aLastName = $aUnit['persons'][0]['lastName'];
            $aFirstName = $aUnit['persons'][0]['firstName'];
            $aEmail = $aUnit['persons'][0]['contacts'][0]['description'];

            $aLocation = $aUnit['locations'];

            foreach( $aLocation as $l ) {
                switch($l['locationClassControlCode']) {
                    case 'POSTAL':
                        $aPostalAttn = $l['attention'];
                        $aPostalAddress = $l['address'];
                        $aPostalCity = $l['city'];
                        $aPostalState = $l['state'];
                        $aPostalZipCode = $l['zipCode'];
                        $aPostalCountry = $l['country'];
                        break;
                    case 'PHYSICAL':
                        $aPhysicalAttn = $l['attention'];
                        $aPhysicalAddress = $l['address'];
                        $aPhysicalCity = $l['city'];
                        $aPhysicalState = $l['state'];
                        $aPhysicalZipCode = $l['zipCode'];
                        $aPhysicalCountry = $l['country'];
                        break;
                }
            }

            $aContacts = $aUnit['contacts'];
            foreach($aContacts as $c) {
                switch($c['contactClassControlCode']) {
                    case 'TELEPHONE':
                        $aTelephone = $c['description'];
                        break;
                    case 'FAX':
                        $aFax = $c['description'];
                        break;
                    case 'MOBILE':
                        $aMobile = $c['description'];
                        break;
                    case 'DIRECT':
                        $aDirect = $c['description'];
                        break;
                    case 'WEBSITE':
                        $aWebsite = $c['description'];
                        break;
                }
            }

            if( array_key_exists('unitParts', $aUnit )) {
                $aUnitParts = $aUnit['unitParts'];
            } else { $aUnitParts = null; }
            if( array_key_exists('vehicles', $aUnit )) {
                $aVehicles = $aUnit['vehicles'];
            } else { $aVehicles = null; }


            // Update Unit
            $unit->setName($aName);
            foreach($unit->getPersons() as $person) {
                if ($person->getIsPrimary() == true) {
                    $person->setLastName($aLastName);
                    $person->setFirstName($aFirstName);
                    foreach($person->getContacts() as $contact){
                        if ($contact->getContactClassControlCode() == 'EMAIL') {
                            $contact->setDescription($aEmail) ;
                        }
                    }
                }
            }
            foreach($unit->getLocations() as $location) {
                switch($location->getLocationClassControlCode()) {
                    case 'POSTAL':
                        $location->setAttention($aPostalAttn);
                        $location->setAddress($aPostalAddress);
                        $location->setCity($aPostalCity);
                        $location->setState($aPostalState);
                        $location->setZipCode($aPostalZipCode);
                        $location->setCountry($aPostalCountry);
                        break;
                    case 'PHYSICAL':
                        $location->setAttention($aPhysicalAttn);
                        $location->setAddress($aPhysicalAddress);
                        $location->setCity($aPhysicalCity);
                        $location->setState($aPhysicalState);
                        $location->setZipCode($aPhysicalZipCode);
                        $location->setCountry($aPhysicalCountry);
                        break;

                }
            }

            if ($aVehicles && count($aVehicles) > 0 && count($unit->getVehicles()) > 0) {
                // search for delete Vehicles
                foreach($unit->getVehicles() as $vehicle){
                    $found = false;
                    foreach($aVehicles as $av) {
                        if ($vehicle->getRegistrationNumber() == $av['registrationNumber']){
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $deleteList[] = $vehicle->getRegistrationNumber();
                        $em->remove($vehicle);
                    }
                }

                // Search for new vehicles
                foreach($aVehicles as $av) {
                    $found = false;
                    foreach($unit->getVehicles() as $vehicle){
                        if ($vehicle->getRegistrationNumber() == $av['registrationNumber']){
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $vCode = strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $av['registrationNumber'])));
                        $addList[] = $vCode;
                        $newVehicle = new \Morus\FasBundle\Entity\Vehicle();
                        $newVehicle->setRegistrationNumber($av['registrationNumber']);
                        $unit->addVehicle($newVehicle);
                        $newVehicle->setUnit($unit);
                    }
                }
            } elseif ($aVehicles) {
                foreach($aVehicles as $v){
                    $addList[] = $v['registrationNumber'];
                    $newVehicle = new \Morus\FasBundle\Entity\Vehicle();
                    $newVehicle->setRegistrationNumber($v['registrationNumber']);
                    $unit->addVehicle($newVehicle);
                    $newVehicle->setUnit($unit);
                }
            }
            
            if ($aUnitParts && count($aUnitParts) > 0 && count($unit->getUnitParts()) > 0) {
                // search for delete unit parts
                foreach($unit->getUnitParts() as $unitParts){
                    $found = false;
                    foreach($aUnitParts as $aup) {
                        if ($unitParts->getParts()->getId() == $aup['parts']){
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $em->remove($unitParts);
                    }
                }

                // Search for new unit parts
                foreach($aUnitParts as $aup) {
                    $found = false;
                    foreach($unit->getUnitParts() as $unitParts){
                        if ($unitParts->getParts()->getId() == $aup['parts']){
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $newUnitParts = new \Morus\FasBundle\Entity\UnitParts();
                        $parts = $em->getRepository('MorusFasBundle:Parts')->find($aup['parts']);
                        $newUnitParts->setDiscount($aup['discount']);
                        $newUnitParts->setParts($parts);
                        $unit->addUnitParts($newUnitParts);
                        $newUnitParts->setUnit($unit);
                    }
                }
            } elseif ($aUnitParts) {
                foreach($aUnitParts as $aup){
                    $newUnitParts = new \Morus\FasBundle\Entity\UnitParts();
                    $parts = $em->getRepository('MorusFasBundle:Parts')->find($aup['parts']);
                    $newUnitParts->setDiscount($aup['discount']);
                    $newUnitParts->setParts($parts);
                    $unit->addUnitParts($newUnitParts);
                    $newUnitParts->setUnit($unit);
                }
            }
            
            
            
            $em->persist($unit);
            $em->flush();
            
            $response = array("success" => true, 'updatedLicences' => $addList, 'deletedLicences' => $deleteList);
        } catch (Exception $ex) {
            $response = array("success" => false);
        }
                
        return new Response(json_encode($response));
    }
    
    /**
     * Return customer update form to dialog
     *
     */
    public function customerDialogAction(Request $request) {
        $action = $request->get('ACTION');
        $licenses = json_decode($request->get('LICENCES'), true);
        
        $aem = $this->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service
        
        if ($action == 'NEW') {
            
            $unit = $aem->createUnit('customer');

        } else { 
            $em = $this->getDoctrine()->getManager();
            $unit = $em->getRepository('MorusFasBundle:Unit')->find($action);
            foreach( $unit->getVehicles() as $vehicle) {
                if(($key = array_search($vehicle->getRegistrationNumber(), $licenses)) !== false) {
                    unset($licenses[$key]);
                }
            }
            
            
        }
        
        foreach ($licenses as $license) {
            $vehicle = new \Morus\FasBundle\Entity\Vehicle();
            $vehicle->setRegistrationNumber($license);
            $unit->addVehicle($vehicle);
            $vehicle->setUnit($unit);
        }
        
        $form = $this->createForm('fas_unit', $unit, array(
            'attr' => array('id' => 'fas_customer_update_form'),
            'action' => $this->generateUrl('morus_fas_statement_customer_update'),
            'method' => 'POST',
        ));
        
        $form->add('submit', 'submit', array(
                'label' => $this->get('translator')->trans('btn.save'),
                'attr' => array('style' => 'display:none')
                ));
        
        return $this->render('MorusFasBundle:Statement:customer.dialog.html.twig', array(
            'unit' => $unit,
            'form'   => $form->createView(),
        ));
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
     * Lists parts entities.
     *
     */
    public function partsListAction(Request $request)
    {
        $itemnames = $request->get('parts');
        $qb = $this->getDoctrine()
                ->getManager()
                ->getRepository('MorusFasBundle:Parts')
                ->createQueryBuilder('p');
            
        $query = $qb
                ->where($qb->expr()->in('p.itemname', $itemnames));
        
        $parts = $query->getQuery()->getResult();
        
        return $this->render('MorusFasBundle:Statement:export.product.list.html.twig', array(
            'parts' => $parts,
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
                    
                    return $this->redirect($this->generateUrl('morus_accetic_ar')); // redirect when done
                }
            }

            // ----------------------------------------------------
            // New Parts Form
            // ----------------------------------------------------
            $parts = new Parts();
            $parts_form = $this->createForm('fas_parts', $parts, array(
                'attr' => array('id' => 'accetic_parts_list'),
                'action' => $this->generateUrl('morus_fas_statement_export_create_product_ajax'),
                'method' => 'POST',
            ));
            
            $parts_form->add('submit', 'submit', array(
                    'label' => $this->get('translator')->trans('btn.save'),
                    'attr' => array('style' => 'display:none')
                ));
            
            return $this->render('MorusFasBundle:Statement:export.html.twig', array(
                'export_form' => $export_form->createView(),
                'parts_form' => $parts_form->createView(),
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
