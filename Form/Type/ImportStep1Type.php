<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImportStep1Type extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('unit', 'entity', array(
                'class' => 'MorusAcceticBundle:Unit',
                'property' => 'name'
            ))
            ->add('splitDateTime', 'checkbox', array(
                'required' => false
            ))
            ->add('name', 'text')
            ->add('file', 'file')
            ;
    }

    public function getName() {
        return 'fas_import_step1';
    }
}