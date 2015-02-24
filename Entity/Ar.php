<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\Ar as BaseAr;

/**
 * Ar
 *
 * @ORM\Table(name="accetic_ar", uniqueConstraints={@ORM\UniqueConstraint(name="ar_invnumber_key", columns={"invnumber"})}, indexes={@ORM\Index(name="IDX_unit_id", columns={"unit_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Ar extends BaseAr
{
    
}
