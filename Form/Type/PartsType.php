<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PartsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('othername' , 'text', array(
                'required' => false
            ))
            ->add('useOthername' , 'checkbox', array(
                'required' => false
            ))
            ->add('defaultDiscount' , 'money', array(
                'currency' => 'HKD',
                'precision' => 2,
                'required' => false
            ))
            ->add('itemcode' , 'text', array(
                'read_only' => true
            ))
            ->add('itemname' , 'text', array(
                'read_only' => true
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Morus\FasBundle\Entity\Parts'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fas_parts';
    }
}
