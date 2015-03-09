<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ImportStep2Type extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event){
                $form = $event->getForm();
                $headers = $event->getData()->getHeaders();
                
                $form->add('cardNumberHeader', 'choice', array(
                    'empty_value' => 'statement.choose_column',
                    'choices' => $headers
                ));
                
                $form->add('licenceNumberHeader', 'choice', array(
                    'empty_value' => 'statement.choose_column',
                    'choices' => $headers
                ));
                
                $form->add('siteHeader', 'choice', array(
                    'empty_value' => 'statement.choose_column',
                    'choices' => $headers
                ));
                
                $form->add('receiptNumberHeader', 'choice', array(
                    'empty_value' => 'statement.choose_column',
                    'choices' => $headers
                ));
                
                if ($event->getData()->getSplitDateTime() == true) {
                    $form->add('transactionDateHeader', 'choice', array(
                        'empty_value' => 'statement.choose_column',
                        'choices' => $headers
                    ));
                    
                    $form->add('transactionTimeHeader', 'choice', array(
                        'empty_value' => 'statement.choose_column',
                        'choices' => $headers
                    ));
                } else {
                    $form->add('transactionDatetimeHeader', 'choice', array(
                        'empty_value' => 'statement.choose_column',
                        'choices' => $headers
                    ));
                }
                
                $form->add('productNameHeader', 'choice', array(
                    'empty_value' => 'statement.choose_column',
                    'choices' => $headers
                ));
                
                $form->add('unitDiscountHeader', 'choice', array(
                    'empty_value' => 'statement.choose_column',
                    'choices' => $headers
                ));
                
                $form->add('volumeHeader', 'choice', array(
                    'empty_value' => 'statement.choose_column',
                    'choices' => $headers
                ));
                
                $form->add('unitPriceHeader', 'choice', array(
                    'empty_value' => 'statement.choose_column',
                    'choices' => $headers
                ));
                
                $form->add('netAmountHeader', 'choice', array(
                    'empty_value' => 'statement.choose_column',
                    'choices' => $headers
                ));
                
            });
    }

    public function getName() {
        return 'fas_import_step2';
    }
}