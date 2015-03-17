<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unit', 'entity', array(
                'class' => 'MorusFasBundle:Unit',
                'property' => 'name'
            ))
            ->add('invnumber', 'text')
            ->add('transdate', 'date', array(
                'input'  => 'datetime',
                'widget' => 'single_text',
                'required' => false
            ))
            ->add('duedate', 'date', array(
                'input'  => 'datetime',
                'widget' => 'single_text',
                'required' => false
            ))
            ->add('reference', 'text', array(
                'required' => false,
            ))
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
            ->add('transaction', 'fas_transaction', array('label' => false));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Morus\FasBundle\Entity\Ar'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fas_ar';
    }
}
