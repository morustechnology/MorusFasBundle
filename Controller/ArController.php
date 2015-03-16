<?php

namespace Morus\FasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Ar controller.
 *
 */
class ArController extends Controller
{
    public function printAction($id)
    {
        $aem = $this->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service

        // Get ar with invoices lines
        $qb = $aem->getArRepository()
                ->createQueryBuilder('ar');
        
        $query = $qb
                ->select('ar')
                ->join('ar.transaction', 't')
                ->leftJoin('t.invoices', 'v')
                ->where($qb->expr()->eq('ar.id', $id));
        
        $ar = $query->getQuery()->getSingleResult();

        if ($ar) {
            $html = $this->renderView('MorusFasBundle:ar:print.html.twig', array(
                'ar' => $ar,
            ));
            $pdfGenerator = $this->get('spraed.pdf.generator');
            $pdfGenerator->generatePDF($html, 'UTF-8');

            return new Response($pdfGenerator->generatePDF($html),
                        200,
                        array(
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => 'attachment; filename="out.pdf"'
                        )
            );
        }
        
        
    }
    
    public function ajaxProdDescAction(Request $request)
    {
        try {
            
            $id = $request->get('id');
            
            $aem = $this->get('morus_accetic.entity_manager');
            
            $qb = $aem->getPartsRepository()
                ->createQueryBuilder('p');
            
            $query = $qb
                    ->where($qb->expr()->eq('p.id', ':id'))
                    ->setParameter('id', $id);

            
            $parts = $query->getQuery()->getSingleResult();
            
            $useOthername = $parts->getUseOthername();
            
            if ( $useOthername == true) {
                $description = $parts->getItemname();
            } else {
                $description = $parts->getOthername();
            }
            
            $response = array(
                "success" => true, 
                "description" => $description,
                );
        } catch (Exception $ex) {
            $response = array("success" => false);
        }
                
        return new Response(json_encode($response)); 
    }
    
    /**
     * Lists all Transaction entities.
     *
     */
    public function indexAction()
    {        
        $aem = $this->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service

        // Get ar with invoices lines
        $sqb = $aem->getArRepository()
                ->createQueryBuilder('ar');
        
        $query = $sqb
                ->select('ar.id, ar.invnumber, ar.reference, ar.transdate, ar.duedate, ar.emailed, SUM(v.qty * (v.sellprice - v.selldiscount)) as amount')
                ->addSelect('u.name as to')
                ->join('ar.unit', 'u')
                ->join('ar.transaction', 't')
                
                ->leftJoin('t.invoices', 'v')
                ->groupBy('ar.id');
        
        $ars = $query->getQuery()->getResult();
        
        return $this->render('MorusFasBundle:Ar:index.html.twig', array(
            'ars' => $ars,
        ));
    }
    
    /**
     * Displays a form to edit an existing Transaction entity.
     *
     */
    public function editAction($id)
    {
        $aem = $this->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service

        // Get ar with invoices lines
        $qb = $aem->getArRepository()
                ->createQueryBuilder('ar');
        
        $query = $qb
                ->select('ar')
                ->join('ar.transaction', 't')
                ->leftJoin('t.invoices', 'v')
                ->where($qb->expr()->eq('ar.id', $id));
        
        $ar = $query->getQuery()->getSingleResult();

        if (!$ar) {
            throw $this->createNotFoundException('Unable to find Ar entity.');
        }

        $editForm = $this->genEditForm($ar);
        $deleteForm = $this->genDeleteForm($id);

        return $this->render('MorusFasBundle:Ar:edit.html.twig', array(
            'ar'      => $ar,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Transaction entity.
    *
    * @param Transaction $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function genEditForm($ar)
    {
        $form = $this->createForm('fas_ar', $ar, array(
            'attr' => array( 'id' => 'morus_fas_ar_edit_form'),
            'action' => $this->generateUrl('morus_fas_ar_update', array('id' => $ar->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
            'label' => false,
            'attr' => array( 'style' => 'display:none')
            ));

        return $form;
    }
    
    /**
     * Edits an existing Transaction entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $aem = $this->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service
        
        // Get ar with invoices lines
        $qb = $aem->getArRepository()
                ->createQueryBuilder('ar');
        
        $query = $qb
                ->select('ar')
                ->join('ar.transaction', 't')
                ->leftJoin('t.invoices', 'v')
                ->where($qb->expr()->eq('ar.id', $id));
        
        $ar = $query->getQuery()->getSingleResult();


        if (!$ar) {
            throw $this->createNotFoundException('Unable to find Transaction entity.');
        }

        $deleteForm = $this->genDeleteForm($id);
        $editForm = $this->genEditForm($ar);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl('morus_fas_ar'));
        }

        return $this->render('MorusFasBundle:Ar:edit.html.twig', array(
            'ar'      => $ar,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Deletes a Transaction entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->genDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $aem = $this->get('morus_accetic.entity_manager'); // Get Accetic Entity Manager from service
        
            $entity = $aem->getTransactionRepository()->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Transaction entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('trans'));
    }

    /**
     * Creates a form to delete a Transaction entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function genDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('morus_fas_ar_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
