<?php

namespace Morus\FasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
//use Doctrine\ORM\EntityRepository;

class ProductListType extends AbstractType
{
    protected $container;
    
    /**
     * 
     * @param Container $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'entity', array(
                'class' => $this->container->getParameter('morus_accetic.model.product'),
                'property' => 'itemname',
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er ) {
                                        return $er->createQueryBuilder('p')
                                                ->where('p.active = 1')
                                                ->orderBy('p.itemname', 'ASC');
                                    }
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fas_product_list';
    }
}
