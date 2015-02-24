<?php

namespace Morus\FasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpFoundation\Response;

use Morus\FasBundle\Entity\Export;
use Morus\FasBundle\Entity\Statement;
use Morus\FasBundle\Entity\StatementProcess;
use Morus\AcceticBundle\Entity\Parts;
use Morus\FasBundle\Entity\PartsExt;
use Morus\FasBundle\Entity\PartsVehicle;



/**
 * Statement controller.
 *
 */
class StatementController extends Controller
{
    private $expStmts;
    
    /**
     * Lists all Statement entities.
     *
     */
    public function indexAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MorusFasBundle:Statement')->findAll();

        $form = $this->createForm('fas_statement_list', $entities, array(
            'action' => $this->generateUrl('morus_fas_statement'),
            'method' => 'POST',
        ));

        $form->add('export_invoice', 'submit', array('label' => $this->get('translator')->trans('statement.export_invoice')));
        $form->add('delete_invoice', 'submit', array('label' => $this->get('translator')->trans('statement.delete_invoice')));
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            if ($form->get('export_invoice')->isClicked()) {
                $expStmts = $form->getData()['id'];
                $session = $this->get('session');
                $session->set('expStmts', $expStmts);
                
                return $this->redirect($this->generateUrl('morus_fas_statement_export'));
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
    public function newProductAction(Request $request) {        
        try {
            
            $index = $request->get('index');
            $data = $request->get('fas_parts_ext');

            $parts = new Parts();
            $parts->setItemcode($data['parts']['itemcode']);
            $parts->setItemname($data['parts']['itemname']);
            $parts->setUnit('L');
            $partsExt = new PartsExt();
            $partsExt->setDefaultDiscount($data['defaultDiscount']);
            $partsExt->setParts($parts);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($parts);
            $em->persist($partsExt);
            //$em->flush();
            
            $response = array(
                "success" => true, 
                "index" => $index,
                "itemcode" => $data['parts']['itemcode'], 
                "itemname" => $data['parts']['itemname'],
                "discount" => $data['defaultDiscount'],
                );
        } catch (Exception $ex) {
            $response = array(
                "success" => false,
                "index" => $index,
                "itemcode" => $data['parts']['itemcode'], 
                "itemname" => $data['parts']['itemname'],
                "discount" => $data['defaultDiscount'],
                );
        }
                
        return new Response(json_encode($response)); 

    }
    
    /**
     * Handle Ajax call for new vehicle
     * 
     */
    
    
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
            foreach($expStmts as $expStmt) {
                $export->addStatement($expStmt);
            }
            
            // ----------------------------------------------------
            // Export Form Flow
            // ----------------------------------------------------
            $flow = $this->get('morus_fas.form.flow.export');
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
//                    $em = $this->getDoctrine()->getManager();
//                    $em->persist($formData);
//                    $em->flush();
                    $flow->reset(); // remove step data from the session

                    return $this->redirect($this->generateUrl('morus_fas_homepage')); // redirect when done
                }
            }

            // ----------------------------------------------------
            // New Parts Form
            // ----------------------------------------------------
            $parts = new Parts();
//            $parts->setItemcode($request->get('itemcode'));
//            $parts->setItemname($request->get('itemname'));
//            $parts->setSale(true);

            $partsExt = new PartsExt();
            $partsExt->setParts($parts);
//            $this->getDoctrine()->getManager()->persist($parts);
//            $this->getDoctrine()->getManager()->persist($partsExt);

            // New Parts Form
            $parts_form = $this->createForm('fas_parts_ext', $partsExt, array(
                'action' => $this->generateUrl('morus_fas_statement_export_new_product'),
                'method' => 'POST',
            ));

            $parts_form->add('submit', 'submit', array(
                    'label' => $this->get('translator')->trans('inventory.new.save'),
                    'attr' => array('style' => 'display:none')
                ));

            
            // ----------------------------------------------------
            // New Vehicle Form
            // ----------------------------------------------------
            $partsVehicle = new PartsVehicle();
            
            // New Parts Form
            $parts_vehicle_form = $this->createForm('fas_parts_vehicle', $partsVehicle, array(
                'action' => $this->generateUrl('morus_fas_statement_export_new_product'),
                'method' => 'POST',
            ));

            $parts_form->add('submit', 'submit', array(
                    'label' => $this->get('translator')->trans('inventory.new.save'),
                    'attr' => array('style' => 'display:none')
                ));
            
            return $this->render('MorusFasBundle:Statement:export.html.twig', array(
                'export_form' => $export_form->createView(),
                'parts_form' => $parts_form->createView(),
                'parts_vehicle_form' => $parts_vehicle_form->createView(),
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
        
        $init = $em
                ->getRepository('MorusFasBundle:StatementStatus')
                ->findOneByControlCode('NEW');
        
        // Initialize new statement
        $statement = new Statement();
        $statement->setStatementStatus($init);
        $statementProcess = new StatementProcess();
        $statementProcess->setStatement($statement);
        
        $flow = $this->get('morus_fas.form.flow.statementImport'); // must match the flow's service id
        $flow->bind($statement);
        
        if ($statement->getFile()) {
            $a = 4+9;
        }
        
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

                return $this->redirect($this->generateUrl('morus_fas_homepage'));
            }
        }

        return $this->render('MorusFasBundle:Statement:import.html.twig', array(
            'form' => $form->createView(),
            'flow' => $flow,
        ));
    }

    
    
    /**
     * Creates a new Statement entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Statement();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('morus_fas_statement_show', array('id' => $entity->getId())));
        }

        return $this->render('MorusFasBundle:Statement:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    
    /**
     * Displays a form to create a new Statement entity.
     *
     */
    public function newAction()
    {
        $entity = new Statement();
        $form   = $this->createCreateForm($entity);

        return $this->render('MorusFasBundle:Statement:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Statement entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MorusFasBundle:Statement')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Statement entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('MorusFasBundle:Statement:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Statement entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MorusFasBundle:Statement')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Statement entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('MorusFasBundle:Statement:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Edits an existing Statement entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MorusFasBundle:Statement')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Statement entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('morus_fas_statement_edit', array('id' => $id)));
        }

        return $this->render('MorusFasBundle:Statement:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Deletes a Statement entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MorusFasBundle:Statement')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Statement entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('morus_fas_statement'));
    }
    
    /**
     * Creates a form to create a Statement entity.
     *
     * @param Statement $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createUploadForm(Statement $entity)
    {
        $form = $this->createForm('fas_statement', $entity, array(
            'action' => $this->generateUrl('morus_fas_statement_upload'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => $this->get('translator')->trans('statement.upload')));

        return $form;
    }
    
    /**
     * Creates a form to create a Statement entity.
     *
     * @param Statement $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Statement $entity)
    {
        $form = $this->createForm('fas_statement', $entity, array(
            'action' => $this->generateUrl('morus_fas_statement_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => $this->get('translator')->trans('statement.create')));

        return $form;
    }
    
    /**
    * Creates a form to edit a Statement entity.
    *
    * @param Statement $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Statement $entity)
    {
        $form = $this->createForm('fas_statement', $entity, array(
            'action' => $this->generateUrl('morus_fas_statement_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    /**
     * Creates a form to delete a Statement entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('morus_fas_statement_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
