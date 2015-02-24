<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\Person as BasePerson;

/**
 * Person
 *
 * @ORM\Table(name="accetic_person", uniqueConstraints={@ORM\UniqueConstraint(name="person_id_key", columns={"id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Person extends BasePerson
{
    
}
