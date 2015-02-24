<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\Contact as BaseContact;

/**
 * Contact
 *
 * @ORM\Table(name="accetic_contact", indexes={@ORM\Index(name="IDX_contact_class_id", columns={"contact_class_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Contact extends BaseContact
{
    
}
