<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InvoiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', 'entity', array(
                'class' => 'MorusFasBundle:Product',
                'property' => 'itemcode',
                'required' => false
            ))
            ->add('transDate', 'date', array(
                'input'  => 'datetime',
                'widget' => 'single_text',
                'required' => false
            ))
            ->add('transTime', 'time', array(
                'input'  => 'datetime',
                'widget' => 'single_text',
                'required' => false
            ))
            ->add('site', 'text', array(
                'required' => false
            ))
            ->add('receiptNumber', 'text', array(
                'required' => false
            ))
            ->add('licence', 'text', array(
                'required' => false
            ))    
            ->add('description', 'text', array(
                'required' => false,
                'read_only' => true,
            ))
            ->add('qty', 'number', array(
                'precision' => 2,
                'required' => false
            ))
            ->add('sellprice', 'number', array(
                'precision' => 2,
                'required' => false
            ))
            ->add('selldiscount', 'number', array(
                'precision' => 2,
                'required' => false
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Morus\FasBundle\Entity\Invoice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fas_invoice';
    }
}
