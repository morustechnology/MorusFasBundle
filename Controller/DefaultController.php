<?php

namespace Morus\FasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MorusFasBundle:Default:index.html.twig');
    }
}
