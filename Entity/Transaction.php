<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\Transaction as BaseTransaction;

/**
 * Transaction
 *
 * @ORM\Table(name="accetic_transaction")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Transaction extends BaseTransaction
{
    
}
