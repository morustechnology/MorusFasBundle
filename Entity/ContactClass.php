<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\ContactClass as BaseContactClass;

/**
 * ContactClass
 *
 * @ORM\Table(name="accetic_contact_class", uniqueConstraints={@ORM\UniqueConstraint(name="contact_class_id_key", columns={"id", "control_code"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ContactClass extends BaseContactClass
{
    
}
