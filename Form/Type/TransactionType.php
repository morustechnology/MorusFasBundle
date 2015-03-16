<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TransactionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('invoices', 'collection', array(
                'label' => false,
                'type' => 'fas_invoice',
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Morus\FasBundle\Entity\Transaction'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fas_transaction';
    }
}
