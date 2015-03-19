<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
   
        $builder
            ->add('locationClassControlCode', 'hidden')
            ->add('address', 'textarea', array(
                        'required' => false
                ))
            ->add('city', 'text', array(
                        'required' => false
                ))
            ->add('state', 'text', array(
                        'required' => false
                ))
            ->add('zipCode', 'text', array(
                        'required' => false
                ))
            ->add('country', 'country', array(
                        'required' => false
                ))
            ->add('attention', 'text', array(
                        'required' => false
                ));
            
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Morus\FasBundle\Entity\Location',
        ));
    }

    
    public function getName()
    {
        return 'fas_location';
    }
}
?>
