<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
   
        $builder
                    ->add('firstName', 'text', array(
                        'required' => false
                    ))
                    ->add('lastName', 'text', array(
                        'required' => false
                    ))
                    ->add('contacts', 'collection', array(
                        'type' => 'accetic_contact',
                        'allow_add'    => true,
                        'allow_delete' => true,
                        'prototype' => true,
                    ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Morus\FasBundle\Entity\Person',
        ));
    }

    
    public function getName()
    {
        return 'fas_person';
    }
}
?>
