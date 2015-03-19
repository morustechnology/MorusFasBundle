<?php

namespace Morus\FasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Ps\PdfBundle\Annotation\Pdf;
use PHPPdf\Core\Configuration\LoaderImpl;
use PHPPdf\Core\FacadeBuilder;

/**
 * Ar controller.
 *
 */
class ArController extends Controller
{
    /**
     * @Pdf(stylesheet="MorusFasBundle:Ar:invoice.stylesheet.twig")
     */
    public function printTestAction($id) {
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
        
        // Get postal address
        $postqb = $aem->getLocationRepository()
                ->createQueryBuilder('l');
        
        $postquery = $postqb
                ->join('l.unit', 'u')
                ->join('l.locationClass', 'lc', 'WITH', 'lc.controlCode = :controlCode')
                ->where('l.unit = :unit')
                ->setParameter('controlCode', 'POSTAL')
                ->setParameter('unit', $ar->getUnit());
                
        $postal = $postquery->getQuery()->getSingleResult();
        
        // Total qty total
        $qty_subtotals = array();
        $amount_subtotals = array();
        $qty_total = 0;
        $amount_total = 0;
        foreach($ar->getTransaction()->getInvoices() as $invoice)
        {
            $vehicle_number = $invoice->getLicence();
            
            if (array_key_exists($vehicle_number, $qty_subtotals)) {
                $qty = $qty_subtotals[$vehicle_number];
                $qty = $qty + $invoice->getQty();
                $qty_subtotals[$vehicle_number] = $qty;
            } else {
                $qty_subtotals[$vehicle_number] = $invoice->getQty();
            }
            
            if (array_key_exists($vehicle_number, $amount_subtotals)) {
                $amt = $amount_subtotals[$vehicle_number];
                $amt = $amt + $invoice->getAmount();
                $amount_subtotals[$vehicle_number] = $amt;
            } else {
                $amount_subtotals[$vehicle_number] = $invoice->getAmount();
            }
            
            $qty_total = $qty_total + round($invoice->getQty(),2);
            $amount_total = $amount_total + round($invoice->getAmount(), 2);
        }
        
        $format = $this->get('request')->get('_format');
        
        return $this->render(sprintf('MorusFasBundle:Ar:invoice.%s.twig', $format), array(
            'ar' => $ar,
            'postal' => $postal,
            'qty_subtotals' => $qty_subtotals,
            'amount_subtotals' => $amount_subtotals,
            'qty_total' => $qty_total,
            'amount_total' => $amount_total
        ));
    }
    
    /**
     * @Pdf(stylesheet="MorusFasBundle:Ar:invoice.stylesheet.twig")
     */
    public function printAction($id) {
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
        
        // Get postal address
        $postqb = $aem->getLocationRepository()
                ->createQueryBuilder('l');
        
        $postquery = $postqb
                ->join('l.unit', 'u')
                ->join('l.locationClass', 'lc', 'WITH', 'lc.controlCode = :controlCode')
                ->where('l.unit = :unit')
                ->setParameter('controlCode', 'POSTAL')
                ->setParameter('unit', $ar->getUnit());
                
        $postal = $postquery->getQuery()->getSingleResult();
        
//        $format = $this->get('request')->get('_format');
        
//        return $this->render(sprintf('MorusFasBundle:Ar:invoice.%s.twig', $format), array(
//            'ar' => $ar,
//            'postal' => $postal,
//        ));
        
        
        $path = $this->container->getParameter('kernel.root_dir') . '/../src/Morus/FasBundle/Resources/views/Ar/invoice.stylesheet.twig';

        $stylesheet = file_get_contents($path);
        
        $facade = $this->get('ps_pdf.facade');
        $response = new Response();
        $this->render('MorusFasBundle:Ar:invoice.pdf.twig', array(
            'ar' => $ar,
            'postal' => $postal,
        ), $response);
        
        $xml = $response->getContent();
        
        $content = $facade->render($xml, $stylesheet);
        
        return new Response($content, 200, array(
            'content-type' => 'application/pdf', 
            'Content-Disposition'   => 'attachment; filename="' . $ar->getUnit()->getName() . '.pdf"')
                );
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
    public function indexAction(Request $request)
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
        
        
        
        
        $form = $this->createForm('fas_ar_list', $ars, array(
            'attr'   => array('id' => 'fas_inv_list_frm'),
            'action' => $this->generateUrl('morus_fas_ar'),
            'method' => 'POST',
        ));

        $form->add('bulk_print', 'submit', array('label' => 'bulk print', 'attr' => array( 'style' => '' )));
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            if ($form->get('bulk_print')->isClicked()) {
                $ids = array();
                $ars = $form->getData()['id'];
                foreach ($ars as $ar) {
                    $ids[] = $ar->getId();
                }
                $session = $this->get('session');
                $session->set('bulk_print_ids', $ids);
                
                return $this->redirect($this->generateUrl('morus_fas_bulk_print'));
            }
        }
        
        
        return $this->render('MorusFasBundle:Ar:index.html.twig', array(
            'ars' => $ars,
            'form' => $form->createView(),
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
