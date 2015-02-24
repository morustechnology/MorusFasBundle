<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PartsExtType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('defaultDiscount' , 'money', array(
                'currency' => 'HKD',
                'precision' => 2,
                'required' => false
            ))
            ->add('parts' , 'fas_parts');
//            ->add('itemcode' , 'text')
//            ->add('itemname' , 'text')
//            ->add('unit' , 'text' , array(
//                'required' => false
//            ))
//            ->add('sale', 'checkbox', array(
//                'required' => false
//            ))
//            ->add('listprice' , 'money', array(
//                'currency' => 'HKD',
//                'precision' => 2,
//                'required' => false
//            ))
//            ->add('saleDescription' , 'text', array(
//                'required' => false
//            ))
//            ->add('purchase', 'checkbox', array(
//                'required' => false
//            ))
//            ->add('lastcost' , 'money', array(
//                'currency' => 'HKD',
//                'precision' => 2,
//                'required' => false
//            ))
//            ->add('purchaseDescription' , 'text', array(
//                'required' => false
//            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Morus\FasBundle\Entity\PartsExt',
            'attr' => ['id' => 'fas_parts_ext']
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fas_parts_ext';
    }
}
