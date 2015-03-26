<?php

namespace Morus\FasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ddeboer\DataImport\Writer\ExcelWriter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Export Controller
 *
 */
class ExportController extends Controller 
{
    protected $pl;
    
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        
        $exports = $em->getRepository('MorusFasBundle:Export')
                ->findAll();
        
        return $this->render('MorusFasBundle:Export:index.html.twig', array(
            'exports' => $exports,
        ));
    }
    
    public function plAction($id) {
        $em = $this->getDoctrine()->getManager();
        
        $export = $em->getRepository('MorusFasBundle:Export')
                ->find($id);
        
        $exportpl = $this->container->get('morus_fas.form.flow.invoice.export.pl');
        $pl = $exportpl->getPL($export->getArs());
        
        return $this->render('MorusFasBundle:Export:pl.html.twig', array(
            'pl' => $pl,
            'id' => $id,
        ));
    }
    
    public function excelAction(Request $request, $id) {
        
        // Get export details
        $em = $this->getDoctrine()->getManager();
        
        $export = $em->getRepository('MorusFasBundle:Export')
                ->find($id);
        
        $exportpl = $this->container->get('morus_fas.form.flow.invoice.export.pl');
        $pl = $exportpl->getPL($export->getArs());
        
        // Create Excel Object
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()
                    ->setCreator("FAS 3.0")
                    ->setLastModifiedBy("FAS 3.0")
                    ->setTitle("Office 2007 XLSX P and L Document")
                    ->setSubject("Office 2007 XLSX P and L Document")
                    ->setDescription("P and L document for Office 2007 XLSX, generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("P and L");
        
        // Init setting
        $aligncenter = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        
        $alignright = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            )
        );
        
        $activeSheet = $objPHPExcel->getActiveSheet(0);
        // Apply Global Style
        $activeSheet->setTitle($export->getName());
        
        $activeSheet->getColumnDimension('A')->setWidth(32);
        $activeSheet->getColumnDimension('B')->setWidth(10);
        $activeSheet->getColumnDimension('C')->setWidth(4);
        $activeSheet->getColumnDimension('D')->setWidth(5);
        $activeSheet->getColumnDimension('E')->setWidth(10);
        $activeSheet->getColumnDimension('F')->setWidth(10);
        $activeSheet->getColumnDimension('G')->setWidth(10);
        $activeSheet->getColumnDimension('H')->setWidth(10);
        $activeSheet->getColumnDimension('I')->setWidth(10);
        $activeSheet->getColumnDimension('J')->setWidth(15);
        $activeSheet->getColumnDimension('K')->setWidth(15);
        
        $objPHPExcel->getDefaultStyle()
                    ->getFont()
                    ->setName('Arila')
                    ->setSize(12);
        
        $objPHPExcel->getDefaultStyle()
                    ->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $activeSheet->getStyle('E')
                    ->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $activeSheet->getStyle('E')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
        
        $activeSheet->getStyle('F')
                    ->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $activeSheet->getStyle('F')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
        
        $activeSheet->getStyle('G')
                    ->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $activeSheet->getStyle('G')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
        
        $activeSheet->getStyle('H')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        
        // Header Style
        $activeSheet->getStyle('A1:D1')->getFill()->applyFromArray(array(
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'FEC088'
            )
        ));
        
        $activeSheet->getStyle('E1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $activeSheet->getStyle('E1')->getFill()->applyFromArray(array(
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'FFFF88'
            ),
        ));
        
        $activeSheet->getStyle('F1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $activeSheet->getStyle('F1')->getFill()->applyFromArray(array(
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'C2FFC1'
            ),
        ));
        
        $activeSheet->getStyle('G1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $activeSheet->getStyle('G1')->getFill()->applyFromArray(array(
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'C2FFFF'
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        ));
        $activeSheet->getStyle('H1:K1')->getFill()->applyFromArray(array(
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'FEC088'
            )
        ));
        // Orange: FEC088 Yellow: FFFF88 Green: C2FFC1 Light BLUE: C2FFFF BLUE: 88C0FE PRUPLE: BF80FF PINK: EC0071
        $activeSheet
                    ->setCellValue('A1', '客戶')
                    ->setCellValue('B1', '')
                    ->setCellValue('C1', 'P')
                    ->setCellValue('D1', '')
                    ->setCellValue('E1', 'Nett')
                    ->setCellValue('F1', '收客')
                    ->setCellValue('G1', 'P/L')
                    ->setCellValue('H1', '%')
                    ->setCellValue('I1', '已收')
                    ->setCellValue('J1', '客戶找數日期')
                    ->setCellValue('K1', '支票號碼');
        
        
        
        $activeSheet->getStyle('1:1')->getFont()->setBold(true);
        $activeSheet->getStyle('A:A')->getFont()->setBold(true);
        
        // Content
        $row = 2;
        foreach($pl as $supplier){
            $activeSheet->setCellValue('A'.$row, $supplier->getName())
                        ->mergeCells('A'.$row.':K'.$row);
                
            $activeSheet->getStyle('A'.$row.':K'.$row)
                        ->getFill()->applyFromArray(array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'startcolor' => array(
                                'rgb' => 'EC0071'
                            )
                        ));
            
            $activeSheet->getStyle('A'.$row.':K'.$row)
                        ->getAlignment()
                        ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $row = $row + 1;
            $startrow = $row;
            foreach($supplier->getCustomers() as $customer) {
                $activeSheet->setCellValue('A'.$row, $customer->getName());
                
                $activeSheet->getStyle('I'.$row)
                        ->getFill()->applyFromArray(array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'startcolor' => array(
                                'rgb' => '88C0FE'
                            )
                        ));
                
                foreach($customer->getCustomerVehicles() as $vehicle) {
                    $activeSheet->setCellValue('B'.$row, $vehicle->getRegistrationNumber())
                                ->setCellValue('C'.$row, '1')
                                ->setCellValue('E'.$row, $vehicle->getNett())
                                ->setCellValue('F'.$row, $vehicle->getReceivable())
                                ->setCellValue('G'.$row, '=SUM(F'.$row.'-E'.$row.')')
                                ->setCellValue('H'.$row, '=SUM(G'.$row.'/E'.$row.')');
                                
                    
                    $row = $row + 1;
                }
                $row = $row + 1;
                
            }
            $endrow = $row;
            $row = $row + 1;
            
            // Total 
            $activeSheet->getStyle('C'.$row.':I'.$row)
                    ->applyFromArray(array(
                        'borders' => array(
                            'top' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THICK
                            ),
                            'bottom' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THICK
                            )
                        )
                    ));

            $activeSheet->setCellValue('C'.$row, '=SUM(C'.$startrow.':C'.$endrow.')');
            $activeSheet->setCellValue('D'.$row, 'Total:');
            $activeSheet->setCellValue('E'.$row, '=SUM(E'.$startrow.':E'.$endrow.')');
            $activeSheet->setCellValue('F'.$row, '=SUM(F'.$startrow.':F'.$endrow.')');
            $activeSheet->setCellValue('G'.$row, '=SUM(G'.$startrow.':G'.$endrow.')');
            $activeSheet->setCellValue('H'.$row, '=SUM(G'.$row.'/E'.$row.')');
            $activeSheet->setCellValue('I'.$row, '=SUM(I'.$startrow.':I'.$endrow.')');
            
            
            $totalRow = $row;
            $row = $row + 1;
            $activeSheet->getStyle('F'.$row.':I'.$row)
                    ->applyFromArray(array(
                        'borders' => array(
                            'left' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THICK
                            ),
                            'bottom' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THICK
                            ),
                            'right' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THICK
                            ),
                        )
                    ));
            
            $activeSheet->getStyle('H'.$row)
                    ->applyFromArray(array(
                        'borders' => array(
                            'left' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THICK
                            ),
                        )
                    ));
            
            $activeSheet->setCellValue('F'.$row, '@');
            $activeSheet->setCellValue('G'.$row, '=SUM(G'.$totalRow.'/C'.$totalRow.')');
            $activeSheet->setCellValue('H'.$row, '尚欠');
            $activeSheet->setCellValue('I'.$row, '=SUM(I'.$totalRow.'-F'.$totalRow.')');
            
            $row = $row + 3;
        }
        

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        
        $objWriter->save('pl/fas_p_l.xlsx'); // Finish writing excel
        

        
        $file = 'pl/fas_p_l.xlsx';
        $downloadName = 'PL'.date('dmYHis').'.xlsx';
        $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($file);
        $response->headers->set('content-type', 'application/vnd.ms-excel');
        $response->setContentDisposition(\Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT, $downloadName);
        
        return $response;
    }
}
