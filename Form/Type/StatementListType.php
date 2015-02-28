<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StatementListType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'entity', array(
                'class' => 'MorusFasBundle:Statement',
                'property' => 'name',
                'expanded' => true,
                'multiple' => true
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
//        $resolver->setDefaults(array(
//            'data_class' => 'Morus\FasBundle\Entity\Statement'
//        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fas_statement_list';
    }
}
