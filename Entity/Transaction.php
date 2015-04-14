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
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Morus\AcceticBundle\Model\InvoiceInterface", mappedBy="transaction", cascade={"persist","remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"licence" = "ASC", "transDate" = "ASC", "transTime" = "ASC"})
     */
    protected $invoices;
}
