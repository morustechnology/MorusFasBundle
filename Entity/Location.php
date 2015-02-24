<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\Location as BaseLocation;

/**
 * Location
 *
 * @ORM\Table(name="accetic_location")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Location extends BaseLocation
{
    
}
