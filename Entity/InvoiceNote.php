<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\InvoiceNote as BaseInvoiceNote;

/**
 * InvoiceNote
 *
 * @ORM\Table(name="accetic_invoice_note", indexes={@ORM\Index(name="IDX_entity_id", columns={"entity_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class InvoiceNote extends BaseInvoiceNote
{
    
}
