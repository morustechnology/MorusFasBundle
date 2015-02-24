<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\UnitClass as BaseUnitClass;

/**
 * UnitClass
 *
 * @ORM\Table(name="accetic_unit_class")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class UnitClass extends BaseUnitClass
{
    
}
