<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UnitType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name' , 'text')
            ->add('persons', 'collection', array(
                'type' => 'accetic_person',
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
            ))
            ->add('locations', 'collection', array(
                'type' => 'accetic_location'
            ))
            ->add('contacts', 'collection', array(
                'type' => 'accetic_contact'
            ))
            ->add('unitParts', 'collection', array(
                'label' => false,
                'type' => 'fas_unit_parts',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
            ->add('vehicles', 'collection', array(
                'label' => false,
                'type' => 'fas_vehicle',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Morus\FasBundle\Entity\Unit'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fas_unit';
    }
}
