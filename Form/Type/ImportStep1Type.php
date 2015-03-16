<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class ImportStep1Type extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('unit', 'entity', array(
                'class' => 'MorusFasBundle:Unit',
                'property' => 'name',
                'query_builder' => function(EntityRepository $ur) {
                    return $ur->createQueryBuilder('u')
                            ->join('u.unitClasses', 'uc', 'WITH', 'uc.controlCode = :ecc')
                            ->setParameter('ecc', 'SUPPLIER')
                            ->where('u.active = 1')
                            ->orderBy('u.name', 'ASC');
                }
            ))
            ->add('splitDateTime', 'checkbox', array(
                'required' => false
            ))
            ->add('name', 'text')
            ->add('datetimeFormat', 'text')
            ->add('dateFormat', 'text')
            ->add('timeFormat', 'text')
            ->add('file', 'file')
            ;
    }

    public function getName() {
        return 'fas_import_step1';
    }
}