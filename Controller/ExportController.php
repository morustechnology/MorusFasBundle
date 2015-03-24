<?php

namespace Morus\FasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Export Controller
 *
 */
class ExportController extends Controller 
{
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
        ));
    }
}
