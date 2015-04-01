<?php

namespace Morus\FasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Morus\FasBundle\Entity\Product;

/**
 * Product controller.
 *
 */
class InventoryController extends Controller
{
    /**
     * Handle Ajax call for deleting Product.
     *
     */
    public function editAjaxAction(Request $request){
        try {
            $data = $request->get('fas_product');
            $id = $request->get('id');
            
            $aem = $this->get('morus_accetic.entity_manager'); // Get Fas Entity Manager from service
            
            $product = $aem->getProductRepository()->find($id);

            if (!$product) {
                throw $this->createNotFoundException('Unable to find Product entity.');
            }
            
            // Edit Item Form
            $form = $this->genEditForm($product);
            
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();
                
                $response = array("success" => true);
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
    
    /**
     * Handle Ajax call for deleting Product.
     *
     */
    public function deleteAjaxAction(Request $request){
        try {
            $ids = array();
            $em = $this->getDoctrine()->getManager();
            foreach($request->request->all() as $req){
                $product = $this->getDoctrine()->getRepository('MorusFasBundle:Product')->findOneById($req);
                $product->setActive(false);
                $em->persist($product);
            }
            $em->flush();
            
            return new Response(json_encode(array(
                "success" => true
                )));
        } catch (Exception $ex) {
            return new Response(json_encode(array(
                "success" => false
                )));
        }
    }
    
    /**
     * Handle Ajax call for creating new Product.
     *
     */
    public function newAjaxAction(Request $request) {
        try {
            
            $aem = $this->get('morus_accetic.entity_manager'); // Get Fas Entity Manager from service
            
            $newProduct = $aem->createProduct();
            $newProduct->setForSale(true);
            
            $form = $this->genCreateForm($newProduct);
            
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($newProduct);
                $em->flush();
                
                $response = array("success" => true);
            } else {
                $response = array("success" => false);
            }
        
            
            return new Response(json_encode($response));
        } catch (Exception $ex) {
            $response = array("success" => false);
            return new Response(json_encode($response));
        }
    }
    
    /**
     * Lists all Product entities.
     *
     */
    public function indexAction(Request $request)
    {
        // List All product
        $aem = $this->get('morus_accetic.entity_manager'); // Get Fas Entity Manager from service
        
        $productRepos = $aem->getProductRepository();
        
        $qb = $productRepos->createQueryBuilder('p')
            ->where('p.active = 1')
            ->orderBy('p.itemname', 'ASC');
        
        $products = $qb->getQuery()->getResult();
        
        $product_list_form = $this->createForm('fas_product_list', $products, array(
            'attr' => array('id' => 'fas_product_list'),
            'action' => $this->generateUrl('morus_fas_inventory_delete_ajax'),
            'method' => 'POST',
        ));
        
        $product_list_form->add('delete_product', 'submit', array(
                'label' => $this->get('translator')->trans('btn.delete'),
                'attr' => array('style' => 'display:none')
            ));
        
        // New Item Form
        $newProduct = $aem->createProduct();
        $newProduct->setForSale(true);
        
        $product_new_form = $this->genCreateForm($newProduct);
        
        return $this->render('MorusFasBundle:Inventory:index.html.twig', array(
            'product_new_form' => $product_new_form->createView(),
            'product_list_form' => $product_list_form->createView(),
            'products' => $products
        ));
    }
    
    /**
     * Creates a form to create a Entity unit.
     *
     * @param Unit $unit The unit
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function genCreateForm($product)
    {
        $form = $this->createForm('fas_product', $product, array(
            'attr' => array('id' => 'fas_product_new'),
            'action' => $this->generateUrl('morus_fas_inventory_new_ajax'),
            'method' => 'POST',
        ));
        
        $form->add('submit', 'submit', array(
            'label' => $this->get('translator')->trans('btn.save'),
            'attr' => array('style' => 'display:none')
        ));

        return $form;
    }
    
    /**
    * Creates a form to edit a Entity unit.
    *
    * @param Entity $unit The Unit
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function genEditForm($product)
    {
        $form = $this->createForm('fas_product', $product, array(
            'attr' => array('id' => 'fas_product_edit'),
            'action' => $this->generateUrl('morus_fas_inventory_edit_ajax'),
            'method' => 'POST',
        ));
        
        $form->add('submit', 'submit', array(
                'label' => $this->get('translator')->trans('btn.save'),
                'attr' => array('style' => 'display:none'),
            ));

        return $form;
    }
    
    /**
     * Finds and displays a Product entity.
     *
     */
    public function showAction($id)
    {
        $aem = $this->get('morus_accetic.entity_manager'); // Get Fas Entity Manager from service

        $product = $aem->getProductRepository()->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        
        // Edit Item Form
        $product_edit_form = $this->genEditForm($product);
        
        return $this->render('MorusFasBundle:Inventory:show.html.twig', array(
            'product'      => $product,
            'product_edit_form' => $product_edit_form->createView(),
        ));
    }
    
    /**
     * Deletes a Product entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $aem = $this->get('morus_accetic.entity_manager'); // Get Fas Entity Manager from service
        $entity = $aem->getProductRepository()->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('morus_fas_inventory'));
    }

    /**
     * Creates a form to delete a Product entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(null, array('attr' => array( 'id' => 'pts_del')))
            ->setAction($this->generateUrl('morus_fas_inventory_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => $this->get('translator')->trans('btn.delete')))
            ->getForm()
        ;
    }
}
