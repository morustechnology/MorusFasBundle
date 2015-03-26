<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\AcceticConfigGroup as BaseAcceticConfigGroup;

/**
 * AcceticConfig
 *
 * @ORM\Table(name="accetic_config_group")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class AcceticConfigGroup extends BaseAcceticConfigGroup
{
    
}
