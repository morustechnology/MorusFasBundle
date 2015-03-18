<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArListType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'entity', array(
                'label' => false,
                'class' => 'MorusFasBundle:Ar',
                'property' => 'invnumber',
                'expanded' => true,
                'multiple' => true
            ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'fas_ar_list';
    }
}
