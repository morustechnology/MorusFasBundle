<?php

namespace Morus\FasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
                ->select('u.id, u.name')
                ->addSelect('p.firstName, p.lastName')
                ->addSelect('c.description')                
                ->join('u.persons', 'p', 'WITH', 'p.isPrimary = 1')
                ->join('p.contacts', 'c')
                ->where('u.active = 1')
                ->orderBy('u.name', 'ASC');

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
                ->select('u.id, u.name')
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
            'edit_form'   => $editForm->createView(),
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

        $deleteForm = $this->genDeleteForm($id);
        $editForm = $this->genEditForm($unit);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($unit);
            $em->flush();

            return $this->redirect($this->generateUrl('morus_fas_contacts'));
        }

        return $this->render('MorusFasBundle:Contacts:edit.html.twig', array(
            'unit'      => $unit,
            'edit_form'   => $editForm->createView(),
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
}
