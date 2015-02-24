<?php

namespace Morus\FasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VehicleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('registrationNumber')
            ->add('sortOrder')
            ->add('active')
            ->add('createDate')
            ->add('lastModifiedDate')
            ->add('inactiveDate')
            ->add('entity')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Morus\FasBundle\Entity\Vehicle'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'morus_fasbundle_vehicle';
    }
}
