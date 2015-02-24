<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\LocationClass as BaseLocationClass;

/**
 * LocationClass
 *
 * @ORM\Table(name="accetic_location_class", uniqueConstraints={@ORM\UniqueConstraint(name="location_class_id_key", columns={"id", "control_code"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class LocationClass extends BaseLocationClass
{
    
}
