<?php

namespace Morus\FasBundle\Classes;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;

/**
 * Handle Export Processes
 *
 * @author Michael
 */
class PL {
    
    public function __construct(EntityManager $entityManager, Container $container) {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }
    
    public function getPL($ars) {
        $suppliers = array();
        foreach($ars as $ar) {
            foreach($ar->getTransaction()->getInvoices() as $invoice) {
                $supplier = $this->getObjectById($invoice->getSuppliergroupid(), $suppliers);
                if( !($supplier)) {
                    $s = $this->entityManager
                        ->getRepository('MorusFasBundle:Unit')
                        ->findOneById($invoice->getSuppliergroupid());
                    
                    if ($s) {
                        $customerVehicle = new CustomerVehicle($invoice->getLicence(), $invoice->getLicence());
                        $customerVehicle->setNett(round($invoice->getNetamount(),2));
                        $customerVehicle->setReceivable(round($invoice->getQty(), 2) * (round($invoice->getSellPrice(), 2) - round($invoice->getSelldiscount(), 2)));
                        
                        $customer = new Customer($ar->getUnit()->getId(), $ar->getUnit()->getName(), $ar->getUnit()->getAccountNumber());
                        $customer->addCustomerVehicle($customerVehicle);
                        
                        $supplier = new Supplier($s->getId(), $s->getName());
                        $supplier->addCustomer($customer);
                        
                        $suppliers[] = $supplier;
                    }
                } else {
                    $customer = $this->getObjectById($ar->getUnit()->getId(), $supplier->getCustomers());
                    if ($customer) { // If customer exist
                        $customerVehicle = $this->getObjectById($invoice->getLicence(), $customer->getCustomerVehicles());
                        if ($customerVehicle) { // If vehicle exist
                            $customerVehicle->accumulateNett(round($invoice->getNetamount(),2));
                            $customerVehicle->accumulateReceivable(round($invoice->getQty(), 2) * (round($invoice->getSellPrice(), 2) - round($invoice->getSelldiscount(), 2)));
                        } else {
                            $customerVehicle = new CustomerVehicle($invoice->getLicence(), $invoice->getLicence());
                            $customerVehicle->setNett(round($invoice->getNetamount(),2));
                            $customerVehicle->setReceivable(round($invoice->getQty(), 2) * (round($invoice->getSellPrice(), 2) - round($invoice->getSelldiscount(), 2)));
                            
                            $customer->addCustomerVehicle($customerVehicle);
                        } // end if vehicle exist
                        
                        
                    } else {  
                        $customerVehicle = new CustomerVehicle($invoice->getLicence(), $invoice->getLicence());
                        $customerVehicle->setNett(round($invoice->getNetamount(),2));
                        $customerVehicle->setReceivable(round($invoice->getQty(), 2) * (round($invoice->getSellPrice(), 2) - round($invoice->getSelldiscount(), 2)));
                            
                        $customer = new Customer($ar->getUnit()->getId(), $ar->getUnit()->getName(), $ar->getUnit()->getAccountNumber());
                        $customer->addCustomerVehicle($customerVehicle);
                        
                        $supplier->addCustomer($customer);
                    } // If customer exist
                    
                    
                }
            }
        } 
        
        return $suppliers;
    }
    
    public function getObjectById($id , $objects) {
        foreach($objects as $object) {
            if ($object->getId() == $id) {
                return $object;
            }
        }
        return null;
    }
    
    
}

    
class Supplier {
    protected $id;
    
    protected $name;
    
    protected $customers;
    
    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
        $this->customerVehicles = array();
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
    
    /**
     * Add customer
     *
     * @param Customer $customer
     * @return plUnit
     */
    public function addCustomer(Customer $customer)
    {
        $this->customers[] = $customer;
        
//        $cus_array = array_reverse($this->customers);
//        
//        foreach($cus_array as $c) {
//            if ( strcmp($customer->getAccountNumber(), $c->getAccountNumber()) < 0) {
//                
//            }
//        }
        
        return $this;
    }

    /**
     * Remove customer
     *
     * @param Customer $customer
     */
    public function removeCustomer(Customer $customer)
    {
        $this->customers->removeElement($customer);
    }

    /**
     * Get customers
     *
     * @return array
     */
    public function getCustomers()
    {
        return $this->customers;
    }
    
    public function getNettTotal() 
    {
        $nettTotal = 0;
        foreach( $this->getCustomers() as $customer) {
            $nettTotal = round($nettTotal + $customer->getNettTotal(), 2);
        }
        return $nettTotal;
    }
    
    public function getReceivableTotal() 
    {
        $receivableTotal = 0;
        foreach( $this->getCustomers() as $customer) {
            $receivableTotal = round($receivableTotal + $customer->getReceivableTotal(), 2);
        }
        return $receivableTotal;
    }
}

class Customer {
    protected $id;
    
    protected $accountNumber;
    
    protected $name;
    
    protected $customerVehicles;
    
    
    
    public function __construct($id, $name, $accountNumber) {
        $this->id = $id;
        $this->name = $name;
        $this->accountNumber = $accountNumber;
        $this->customerVehicles = array();
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setAccountNumber($accountNumber) {
        $this->accountNumber = $accountNumber;
    }
    
    public function getAccountNumber() {
        return $this->accountNumber;
    }
    
    /**
     * Add customerVehicle
     *
     * @param CustomerVehicle $customerVehicle
     * @return plUnit
     */
    public function addCustomerVehicle(CustomerVehicle $customerVehicle)
    {
        $this->customerVehicles[] = $customerVehicle;

        return $this;
    }

    /**
     * Remove customerVehicle
     *
     * @param CustomerVehicle $customerVehicle
     */
    public function removeCustomerVehicle(CustomerVehicle $customerVehicle)
    {
        $this->customerVehicles->removeElement($customerVehicle);
    }

    /**
     * Get customerVehicles
     *
     * @return array
     */
    public function getCustomerVehicles()
    {
        return $this->customerVehicles;
    }
    
    public function getNettTotal() 
    {
        $nettTotal = 0;
        foreach( $this->getCustomerVehicles() as $customerVehicle) {
            $nettTotal = round($nettTotal + $customerVehicle->getNett(), 2);
        }
        return $nettTotal;
    }
    
    public function getReceivableTotal() 
    {
        $receivableTotal = 0;
        foreach( $this->getCustomerVehicles() as $customerVehicle) {
            $receivableTotal = round($receivableTotal + $customerVehicle->getReceivable(), 2);
        }
        return $receivableTotal;
    }
    

}

class CustomerVehicle {
    protected $id;
    protected $registrationNumber;
    protected $nett;
    protected $receivable;
    protected $qty;
    protected $amount;
    
    public function __construct($id, $registrationNumber) {
        $this->id = $id;
        $this->registrationNumber = $registrationNumber;;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setRegistrationNumber($registrationNumber) {
        $this->registrationNumber = $registrationNumber;
    }
    
    public function getRegistrationNumber() {
        return $this->registrationNumber;
    }
    
    public function accumulateNett($nett) {
        $this->nett = $this->nett + round($nett, 2);
    }
    
    public function setNett($nett) {
        $this->nett = round($nett, 2);
    }
    
    public function getNett() {
        return round($this->nett,2);
    }
    
    public function accumulateReceivable($receivable) {
        $this->receivable = $this->receivable + round($receivable, 2);
    }
    
    public function setReceivable($receivable) {
        $this->receivable = round($receivable,2);
    }
    
    public function getReceivable() {
        return round($this->receivable,2);
    }
    
    public function getPl() {
        return round($this->receivable - $this->nett, 2);
    }
    
    public function getPercentage() {
        
        return round(($this->getPl() / $this->getNett()) * 100, 2);
        
    }
    
}