<?php

namespace Morus\FasBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $translator = $this->container->get('translator');
        $menu = $factory->createItem('root');
        
        $menu->addChild($translator->trans('menu.home'), array('route' => 'morus_fas_homepage'));
        $menu->addChild($translator->trans('menu.statement'), array('route' => 'morus_fas_statement'));
        $menu[$translator->trans('menu.statement')]->addChild($translator->trans('menu.statement_import'), array('route' => 'morus_fas_statement_import'));
        $menu[$translator->trans('menu.statement')]->addChild($translator->trans('menu.export_history'), array('route' => 'morus_fas_export'));
//        $menu->addChild($translator->trans('menu.account'));
//        $menu[$translator->trans('menu.account')]->addChild($translator->trans('menu.account_sales'), array('route' => 'morus_fas_ar'));
        $menu->addChild($translator->trans('menu.account_sales'), array('route' => 'morus_fas_ar'));
        $menu->addChild($translator->trans('menu.contacts'), array('route' => 'morus_fas_contacts'));
        $menu->addChild($translator->trans('menu.settings'), array('route' => 'morus_accetic_config_invoice'));
        $menu[$translator->trans('menu.settings')]->addChild($translator->trans('menu.settings_inventory'), array('route' => 'morus_fas_inventory'));
        $menu[$translator->trans('menu.settings')]->addChild($translator->trans('menu.settings_site'), array('route' => 'morus_fas_site'));
        
        return $menu;
    }
}