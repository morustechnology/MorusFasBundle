<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\AcceticConfig as BaseAcceticConfig;

/**
 * AcceticConfig
 *
 * @ORM\Table(name="accetic_config")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class AcceticConfig extends BaseAcceticConfig
{
    
}
