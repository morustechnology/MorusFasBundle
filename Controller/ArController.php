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
    public function excelAction($id) {
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
                if ($invoice->getProduct()->getNonfuelitem() != true) {
                    $qty = $qty_subtotals[$vehicle_number];
                    $qty = $qty + $invoice->getQty();
                    $qty_subtotals[$vehicle_number] = $qty;
                } 
            } else {
                if ($invoice->getProduct()->getNonfuelitem() != true) {
                    $qty_subtotals[$vehicle_number] = $invoice->getQty();
                } else {
                    $qty_subtotals[$vehicle_number] = 0;
                }
                
            }
            
            if (array_key_exists($vehicle_number, $amount_subtotals)) {
                $amt = $amount_subtotals[$vehicle_number];
                $amt = $amt + $invoice->getAmount();
                $amount_subtotals[$vehicle_number] = $amt;
            } else {
                $amount_subtotals[$vehicle_number] = $invoice->getAmount();
            }
            
            if ($invoice->getProduct()->getNonfuelitem() != true) {
                $qty_total = $qty_total + round($invoice->getQty(),2);
            }
            $amount_total = $amount_total + round($invoice->getAmount(), 2);
        }
        
        // Create Excel Object
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()
                    ->setCreator("FAS 3.0")
                    ->setLastModifiedBy("FAS 3.0")
                    ->setTitle("Office 2007 XLSX P and L Document")
                    ->setSubject("Office 2007 XLSX P and L Document")
                    ->setDescription("P and L document for Office 2007 XLSX, generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("Invoice");
        
        
        
        // Global Setting
        
        
        $objPHPExcel->getDefaultStyle()
                    ->getFont()
                    ->setName('Franklin Gothic Demi')
                    ->setSize(12);
        
        $activeSheet = $objPHPExcel->getActiveSheet(0);
        $pageMargins = $activeSheet->getPageMargins();
        $pageMargins->setTop(0.9906);
        $pageMargins->setLeft(0.3048);
        $pageMargins->setRight(0.3048);
        $pageMargins->setBottom(0.9906);
        
        $activeSheet->getColumnDimension('A')->setWidth(6.3928);
        $activeSheet->getColumnDimension('B')->setWidth(5.9285);
        $activeSheet->getColumnDimension('C')->setWidth(14.8571);
        $activeSheet->getColumnDimension('D')->setWidth(5.9285);
        $activeSheet->getColumnDimension('E')->setWidth(7.8214);
        $activeSheet->getColumnDimension('F')->setWidth(7.3571);
        $activeSheet->getColumnDimension('G')->setWidth(7.3571);
        $activeSheet->getColumnDimension('H')->setWidth(7.3571);
        $activeSheet->getColumnDimension('I')->setWidth(7.3571);
        $activeSheet->getColumnDimension('J')->setWidth(1.9642);
        
        // Header
        $activeSheet->setCellValue('A1', '晴朗汽車有限公司');
        $activeSheet->getStyle('A1')->applyFromArray(array(
            'font' => array(
                'bold' => true,
                'size' => '26',
                'name' => 'Lantinghei SC Demibold'
            )
        ));
        
        
        $activeSheet->setCellValue('A2', 'Sunny Day Motors Ltd');
        $activeSheet->getStyle('A2')->applyFromArray(array(
            'font' => array(
                'bold' => false,
                'size' => '22',
                'name' => 'Franklin Gothic Demi'
            )
        ));
        
        $activeSheet->setCellValue('A3', 'P.O. Box No.1563 Yuen Long Post Office');
        $activeSheet->getStyle('A3')->applyFromArray(array(
            'font' => array(
                'bold' => false,
                'size' => '14',
                'name' => 'Franklin Gothic Demi'
            )
        ));
        
        $activeSheet->setCellValue('A4', 'sunnydaymotorsltd@yahoo.com.hk')
                    ->setCellValue('A5', '電話：90901108');
        $activeSheet->getStyle('A4:A5')->applyFromArray(array(
            'font' => array(
                'bold' => false,
                'size' => '12',
                'name' => '新細明體'
            )
        ));
        
        
        $styleArray = array(
            'borders' => array(
              'top' => array(
                'style' => \PHPExcel_Style_Border::BORDER_DOUBLE
              )
            )
          );
        
        $objPHPExcel->getActiveSheet()->getStyle('A7:I7')->applyFromArray($styleArray);
        
        $activeSheet->setCellValue('H8', '發票編號：');
        $activeSheet->setCellValue('I8', $ar->getInvnumber());
        $activeSheet->setCellValue('H9', '發單日期：');
        $activeSheet->setCellValue('I9', $ar->getTransdate()->format('d/m/Y'));
        
        
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        
        $objWriter->save('pl/invoice.xlsx'); // Finish writing excel
        
        
        
        $file = 'pl/invoice.xlsx';
        $downloadName = 'PL'.date('dmYHis').'.xlsx';
        $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($file);
        $response->headers->set('content-type', 'application/vnd.ms-excel');
        $response->setContentDisposition(\Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT, $downloadName);
        
//        return $response;
        return null;
    }
    
    private function genPDF($id, $px = true, $name) 
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
        
        $name = $ar->getUnit()->getName();
                
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
        $vehicles = array();
        $qty_subtotals = array();
        $amount_subtotals = array();
        $qty_total = 0;
        $amount_total = 0;
        foreach($ar->getTransaction()->getInvoices() as $invoice)
        {
            $vehicle_number = $invoice->getLicence();
            
            if (!array_key_exists($vehicle_number, $vehicles)) {
                $vehicles[$vehicle_number] = $vehicle_number;
            }
            
            if (array_key_exists($vehicle_number, $qty_subtotals)) {
                if ($invoice->getProduct()->getNonfuelitem() != true) {
                    $qty = $qty_subtotals[$vehicle_number];
                    $qty = $qty + $invoice->getQty();
                    $qty_subtotals[$vehicle_number] = $qty;
                } 
            } else {
                if ($invoice->getProduct()->getNonfuelitem() != true) {
                    $qty_subtotals[$vehicle_number] = $invoice->getQty();
                } else {
                    $qty_subtotals[$vehicle_number] = 0;
                }
                
            }
            
            if (array_key_exists($vehicle_number, $amount_subtotals)) {
                $amt = $amount_subtotals[$vehicle_number];
                $amt = $amt + $invoice->getAmount();
                $amount_subtotals[$vehicle_number] = $amt;
            } else {
                $amount_subtotals[$vehicle_number] = $invoice->getAmount();
            }
            
            if ($invoice->getProduct()->getNonfuelitem() != true) {
                $qty_total = $qty_total + round($invoice->getQty(),2);
            }
            $amount_total = $amount_total + round($invoice->getAmount(), 2);
        }
        
        $path = $this->container->getParameter('kernel.root_dir') . '/../src/Morus/FasBundle/Resources/views/Ar/invoice.stylesheet.twig';

        $stylesheet = file_get_contents($path);
        
        $facade = $this->get('ps_pdf.facade');
        $response = new Response();
        $this->render('MorusFasBundle:Ar:invoice.pdf.twig', array(
            'ar' => $ar,
            'postal' => $postal,
            'vehicles' => $vehicles,
            'qty_subtotals' => $qty_subtotals,
            'amount_subtotals' => $amount_subtotals,
            'qty_total' => $qty_total,
            'amount_total' => $amount_total,
            'px' => $px
        ), $response);
        
        $xml = $response->getContent();
        
        $content = $facade->render($xml, $stylesheet);
        
        return $content;
    }
    /**
     * @Pdf(stylesheet="MorusFasBundle:Ar:invoice.stylesheet.twig")
     */
    public function printNoPxAction($id) {
        
        $filename = 'initial';
        $content = $this->genPDF($id, false, $filename);
        
        return new Response($content, 200, array(
            'content-type' => 'application/pdf', 
            'Content-Disposition'   => 'inline; filename="' . $filename . '.pdf"')
                );
    }
    
    /**
     * @Pdf(stylesheet="MorusFasBundle:Ar:invoice.stylesheet.twig")
     */
    public function printAction($id) {
        
        $filename = 'initial';
        $content = $this->genPDF($id, true, $filename);
        
        return new Response($content, 200, array(
            'content-type' => 'application/pdf', 
            'Content-Disposition'   => 'inline; filename="' . $filename . '.pdf"')
                );
    }
    
    public function ajaxProdDescAction(Request $request)
    {
        try {
            
            $id = $request->get('id');
            
            $aem = $this->get('morus_accetic.entity_manager');
            
            $qb = $aem->getProductRepository()
                ->createQueryBuilder('p');
            
            $query = $qb
                    ->where($qb->expr()->eq('p.id', ':id'))
                    ->setParameter('id', $id);

            
            $product = $query->getQuery()->getSingleResult();
            
            $useOthername = $product->getUseOthername();
            
            if ( $useOthername == true) {
                $description = $product->getOthername();
            } else {
                $description = $product->getItemname();
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
        $ars = $aem->getArRepository()
                ->findAll();
        
        $query = $aem->getArRepository()
                ->createQueryBuilder('ar')
                ->where('ar.active = 1')
                ->orderBy('ar.invnumber', 'DESC')
                ->getQuery();
                
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            30/*limit per page*/
        );
        
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
                $data = $form->getData();
                $ars = $data['id'];
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
            'pagination' => $pagination,
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
                ->leftJoin('t.invoices', 'i')
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
