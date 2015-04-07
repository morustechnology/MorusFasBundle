<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ExportStep1Type extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', 'text')
            ->add('ignoreKeywords' , 'text')
            ->add('startdate', 'date', array(
                'input'  => 'datetime',
                'widget' => 'single_text',
                'required' => false
            ))
            ->add('enddate', 'date', array(
                'input'  => 'datetime',
                'widget' => 'single_text',
                'required' => false
            ))
            ->add('replaceStationName', 'checkbox', array(
                'required' => false
            ));
    }
    
    public function getName() {
        return 'fas_export_step1';
    }
}