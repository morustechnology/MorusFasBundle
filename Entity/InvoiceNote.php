<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\InvoiceNote as BaseInvoiceNote;

/**
 * InvoiceNote
 *
 * @ORM\Table(name="accetic_invoice_note", indexes={@ORM\Index(name="IDX_invoice_id", columns={"invoice_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class InvoiceNote extends BaseInvoiceNote
{
    
}
