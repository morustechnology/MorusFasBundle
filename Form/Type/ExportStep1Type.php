<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ExportStep1Type extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ;
    }

    public function getName() {
        return 'fas_export_step1';
    }
}