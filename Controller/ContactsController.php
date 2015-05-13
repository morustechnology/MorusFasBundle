<?php

namespace Morus\FasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contact controller.
 *
 */
class ContactsController extends Controller
{
    /**
     * Contacts Main Page.
     *
     */
    public function indexAction(Request $request)
    {
        return $this->render('MorusFasBundle:Contacts:index.html.twig');
    }
    
    /**
     * Lists Unit entities.
     *
     */
    public function listAjaxAction($ecc)
    {
        $controlCode = strtoupper($ecc);
        
        $aem = $this->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service
        $unitRepos = $aem->getUnitRepository();
        
                $qb = $unitRepos->createQueryBuilder('u')
                    ->addSelect('v')
                    ->leftJoin('u.vehicles', 'v')
                    ->leftJoin('u.unitProducts', 'uv')
                    ->where('u.active = 1')
                    ->orderBy('u.accountNumber', 'DESC');
        
        if ($controlCode == 'ALL') {
            $qb->leftJoin('u.unitClasses', 'uc');
        } else {
            $qb->join('u.unitClasses', 'uc', 'WITH', 'uc.controlCode = :ecc')
            ->setParameter('ecc', $ecc);
        }
        
        $contacts = $qb->getQuery()->getResult();
        
        return $this->render('MorusFasBundle:Contacts:list.html.twig', array(
            'contacts' => $contacts,
        ));
    }
    
    /**
     * Displays a form to create a new Entity unit.
     *
     */
    public function newAction($ecc)
    {        
        $aem = $this->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service
        $unit = $aem->createUnit($ecc);
        
        $form = $this->genCreateForm($unit, $ecc);
        
        return $this->render('MorusFasBundle:Contacts:new.html.twig', array(
            'unit' => $unit,
            'form'   => $form->createView(), 
        ));
    }
    
    /**
     * Creates a new Unit entity.
     *
     */
    public function createAction(Request $request, $ecc)
    {
        $aem = $this->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service
        $unit = $aem->createUnit($ecc);
        
        $form = $this->genCreateForm($unit, $ecc);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($unit);
            $em->flush();

            return $this->redirect($this->generateUrl('morus_fas_contacts'));
        }

        return $this->render('MorusFasBundle:Contacts:new.html.twig', array(
            'unit' => $unit,
            'form'   => $form->createView(),
        ));
    }
    
    /**
     * Creates a form to create a Entity unit.
     *
     * @param Unit $unit The unit
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function genCreateForm($unit, $ecc = null)
    {
        $form = $this->createForm('fas_unit', $unit, array(
            'action' => $this->generateUrl('morus_fas_contacts_create', array('ecc' => $ecc)),
            'method' => 'POST',
        ));
        
        $form->add('submit', 'submit', array(
                'label' => $this->get('translator')->trans('btn.save'),
                'attr' => array('class' => 'green-btn')
                ));
        
        $form->add('cancel', 'submit', array(
                'label' => $this->get('translator')->trans('btn.cancel'),
                'attr' => array('class' => 'green-btn')
                ));

        return $form;
    }
    
    /**
     * Finds and displays a Entity unit.
     *
     */
    public function showAction($id)
    {
        $aem = $this->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service
        
        // Get Contact Info
        $unitRepos = $aem->getUnitRepository();

        $qb = $unitRepos->createQueryBuilder('u')
                ->select('u.id, u.name, u.accountNumber')
                ->addSelect('p.firstName, p.lastName')
                ->addSelect('c.description as email')                
                ->join('u.persons', 'p', 'WITH', 'p.isPrimary = 1')
                ->join('p.contacts', 'c')
                ->where('u.active = 1')
                ->andWhere('u.id = :id')
                ->setParameter('id', $id);
        
        $contact = $qb->getQuery()->getSingleResult();
        
        if (!$contact) {
            throw $this->createNotFoundException('Unable to find contact.');
        }
        
        // Get Phone Number
        $contactRepos = $aem->getContactRepository();
        
        $phoneSql = $contactRepos->createQueryBuilder('c')
                ->select('c.description')
                ->join('c.unit', 'u', 'WITH', 'u.id = :id')
                ->join('c.contactClass', 'cc', 'WITH', 'cc.controlCode = :telephone')
                ->setParameter('id', $id)
                ->setParameter('telephone', 'TELEPHONE');
        $phone = $phoneSql->getQuery()->getSingleResult();
        
        // Get Mobile Number
        $mobileContactRepos = $aem->getContactRepository();
        
        $mobileSql = $mobileContactRepos->createQueryBuilder('c')
                ->select('c.description')
                ->join('c.unit', 'u', 'WITH', 'u.id = :id')
                ->join('c.contactClass', 'cc', 'WITH', 'cc.controlCode = :mobile')
                ->setParameter('id', $id)
                ->setParameter('mobile', 'MOBILE');
        $mobile = $mobileSql->getQuery()->getSingleResult();
        
        // Get Postal Address        
        $locRepos = $aem->getLocationRepository();
        
        $postalSql = $locRepos->createQueryBuilder('l')
                ->join('l.unit', 'u', 'WITH', 'u.id = :id')
                ->join('l.locationClass', 'lc', 'WITH', 'lc.controlCode = :postal')
                ->setParameter('id', $id)
                ->setParameter('postal', 'POSTAL');
        $postal = $postalSql->getQuery()->getSingleResult();
        
        // Get Physical Address        
        $physicalSql = $locRepos->createQueryBuilder('l')
                ->join('l.unit', 'u', 'WITH', 'u.id = :id')
                ->join('l.locationClass', 'lc', 'WITH', 'lc.controlCode = :postal')
                ->setParameter('id', $id)
                ->setParameter('postal', 'PHYSICAL');
        $physical = $physicalSql->getQuery()->getSingleResult();

        $deleteForm = $this->genDeleteForm($id);

        return $this->render('MorusFasBundle:Contacts:show.html.twig', array(
            'contact'       => $contact,
            'phone'         => $phone,
            'mobile'        => $mobile,
            'postal'        => $postal,
            'physical'      => $physical,
            'delete_form'   => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Entity unit.
     *
     */
    public function editAction($id)
    {
        $aem = $this->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service
        $unitRepos = $aem->getUnitRepository();

        $unit = $unitRepos->find($id);

        if (!$unit) {
            throw $this->createNotFoundException('Unable to find Entity unit.');
        }

        $editForm = $this->genEditForm($unit);
        $deleteForm = $this->genDeleteForm($id);

        return $this->render('MorusFasBundle:Contacts:edit.html.twig', array(
            'unit'      => $unit,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Entity unit.
    *
    * @param Entity $unit The Unit
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function genEditForm($unit)
    {
        $form = $this->createForm('fas_unit', $unit, array(
            'action' => $this->generateUrl('morus_fas_contacts_update', array('id' => $unit->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
            'label' => $this->get('translator')->trans('btn.save'),
            'attr' => array('class' => 'green-btn')
        ));
        
        $form->add('cancel', 'submit', array(
                'label' => $this->get('translator')->trans('btn.cancel'),
                'attr' => array('class' => 'green-btn')
                ));

        return $form;
    }
    
    /**
     * Edits an existing Entity unit.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $aem = $this->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service
        $unitRepos = $aem->getUnitRepository();
        
        $unit = $unitRepos->find($id);
        
        if (!$unit) {
            throw $this->createNotFoundException('Unable to find Entity unit.');
        }
        
        $originalVehicles = new ArrayCollection();
        foreach( $unit->getVehicles() as $vehicle){
            $originalVehicles->add($vehicle);
        }
        
        $originalUnitProducts = new ArrayCollection();
        foreach( $unit->getUnitProducts() as $unitProduct){
            $originalUnitProducts->add($unitProduct);
        }
        
        $deleteForm = $this->genDeleteForm($id);
        $editForm = $this->genEditForm($unit);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            // remove the vehicle from database
            foreach ($originalVehicles as $vehicle) {
                if (false === $unit->getVehicles()->contains($vehicle)) {
                     $em->remove($vehicle);
                }
            }

            // remove the unitproduct from database
            foreach ($originalUnitProducts as $unitProduct) {
                if (false === $unit->getUnitProducts()->contains($unitProduct)) {
                     $em->remove($unitProduct);
                }
            }
            
            $em->persist($unit);
            $em->flush();

            return $this->redirect($this->generateUrl('morus_fas_contacts'));
        }

        return $this->render('MorusFasBundle:Contacts:edit.html.twig', array(
            'unit'      => $unit,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Deletes a Entity unit.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->genDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $unit = $em->getRepository('MorusFasBundle:Unit')->find($id);

            if (!$unit) {
                throw $this->createNotFoundException('Unable to find Entity unit.');
            }

            $em->remove($unit);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('morus_fas_contacts'));
    }

    /**
     * Creates a form to delete a Entity unit by id.
     *
     * @param mixed $id The unit id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function genDeleteForm($id)
    {
        return $this->createFormBuilder(null, array('attr' => array( 'id' => 'ct_del_form', 'style' => 'display:none')))
            ->setAction($this->generateUrl('morus_fas_contacts_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => $this->get('translator')->trans('btn.delete')))
            ->getForm();
    }
    
    //*****************************************************************
    // Contacts Dialog Section
    //*****************************************************************
    
    /**
     * Return customer update form to dialog
     *
     */
    public function dialogAction(Request $request) {
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
        
        $form = $this->genDialogForm($unit);
        
        return $this->render('MorusFasBundle:Contacts:dialog.html.twig', array(
            'unit' => $unit,
            'form'   => $form->createView(),
        ));
    }
    
    public function dialogUpdateAction(Request $request) {
        try {
            $action = $request->get('ACTION');
//            $licenses = json_decode($request->get('LICENCES'), true);

            $aem = $this->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service

            if ($action == 'NEW') {
                $unit = $aem->createUnit('customer');
            } else { 
                $em = $this->getDoctrine()->getManager();
                $unit = $em->getRepository('MorusFasBundle:Unit')->find($action);
                
                // Create an ArrayCollection of the current Tag objects in the database
                $originalVehicles = new ArrayCollection();
                foreach ($unit->getVehicles() as $vehicle) {
                    $originalVehicles->add($vehicle);
                }
            }
            
            if (!$unit) {
                throw $this->createNotFoundException('No customer infor found.');
            }

            // Edit Item Form
            $form = $this->genDialogForm($unit);
            
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                
                $addList = array();
                $deleteList = array();
                if ($action == 'NEW') {
                    // update addlist
                    foreach ($unit->getVehicles() as $vehicle) {
                        $vCode = strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $vehicle->getRegistrationNumber())));
                        $addList[] = $vCode;
                    }
                } else { 
                    // remove the vehicle from database and update deletelist
                    foreach ($originalVehicles as $vehicle) {
                        if (false === $unit->getVehicles()->contains($vehicle)) {
                            $vCode = strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $vehicle->getRegistrationNumber())));
                            $deleteList[] = $vCode;
                            $em->remove($vehicle);
                        }
                    }

                    // update addlist
                    foreach ($unit->getVehicles() as $vehicle) {
                        if (false === $originalVehicles->contains($vehicle)) {
                            $vCode = strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $vehicle->getRegistrationNumber())));
                            $addList[] = $vCode;
                        }
                    }
                }
                $em->persist($unit);
                $em->flush();
                
                $response = array("success" => true, 'updatedLicences' => $addList, 'deletedLicences' => $deleteList);
            } else {
                $response = array("success" => false);
            }
            
            return new Response(json_encode($response));
        } catch (Exception $ex) {
            return new Response(json_encode(array(
                "success" => false
                )));
        }
    }
    
    private function genDialogForm($unit) {
        $form = $this->createForm('fas_unit', $unit, array(
                'attr' => array('id' => 'fas_customer_update_form'),
                'action' => $this->generateUrl('morus_fas_contacts_dialog_update'),
                'method' => 'POST',
            ));
        
        $form->add('submit', 'submit', array(
                'label' => $this->get('translator')->trans('btn.save'),
                'attr' => array('style' => 'display:none')
                ));
        
        return $form;
    }
}
