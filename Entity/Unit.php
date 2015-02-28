<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Morus\AcceticBundle\Entity\Unit as BaseUnit;

/**
 * Unit
 *
 * @ORM\Table(name="accetic_unit", uniqueConstraints={@ORM\UniqueConstraint(name="unit_id_key", columns={"id"}), @ORM\UniqueConstraint(name="unit_account_number_key", columns={"account_number"})}, indexes={@ORM\Index(name="IDX_account_number", columns={"account_number"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Unit extends BaseUnit
{
    /**
     * @ORM\OneToMany(targetEntity="Statement", mappedBy="statementStatus")
     **/
    protected $statements;
    
    /**
     * Add statement
     *
     * @param \Morus\FasBundle\Entity\Statement $statement
     * @return StatementStatus
     */
    public function addStatement(\Morus\FasBundle\Entity\Statement $statement)
    {
        $this->statements[] = $statement;

        return $this;
    }

    /**
     * Remove statement
     *
     * @param \Morus\FasBundle\Entity\Statement $statement
     */
    public function removeStatement(\Morus\FasBundle\Entity\Statement $statement)
    {
        $this->statements->removeElement($statement);
    }

    /**
     * Get statements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStatements()
    {
        return $this->statements;
    }
}
