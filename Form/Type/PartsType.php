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
            'data_class' => 'Morus\AcceticBundle\Entity\Parts',
            'attr' => ['id' => 'accetic_parts']
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
