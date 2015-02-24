<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\Ap as BaseAp;

/**
 * Ap
 *
 * @ORM\Table(name="accetic_ap", indexes={@ORM\Index(name="IDX_unit_id", columns={"unit_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Ap extends BaseAp
{
    
}
