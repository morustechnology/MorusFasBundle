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
     * @ORM\OneToMany(targetEntity="Statement", mappedBy="unit", cascade={"persist"})
     **/
    protected $statements;
    
    /**
     * @ORM\OneToMany(targetEntity="Vehicle", mappedBy="unit", cascade={"persist"})
     **/
    protected $vehicles;
    
    /**
     * @ORM\OneToMany(targetEntity="UnitParts", mappedBy="unit", cascade={"persist"})
     **/
    protected $unitParts;
    
    /**
     * Add statement
     *
     * @param \Morus\FasBundle\Entity\Statement $statement
     * @return Unit
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
    
    
    
    /**
     * Add vehicle
     *
     * @param \Morus\FasBundle\Entity\Vehicle $vehicle
     * @return Unit
     */
    public function addVehicle(\Morus\FasBundle\Entity\Vehicle $vehicle)
    {
        $this->vehicles[] = $vehicle;

        return $this;
    }

    /**
     * Remove vehicle
     *
     * @param \Morus\FasBundle\Entity\Vehicle $vehicle
     */
    public function removeVehicle(\Morus\FasBundle\Entity\Vehicle $vehicle)
    {
        $this->vehicles->removeElement($vehicle);
    }

    /**
     * Get vehicles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVehicles()
    {
        return $this->vehicles;
    }

    /**
     * Add unitParts
     *
     * @param \Morus\FasBundle\Entity\UnitParts $unitParts
     * @return Unit
     */
    public function addUnitParts(\Morus\FasBundle\Entity\UnitParts $unitParts)
    {
        $this->unitParts[] = $unitParts;

        return $this;
    }

    /**
     * Remove unitParts
     *
     * @param \Morus\FasBundle\Entity\UnitParts $unitParts
     */
    public function removeUnitParts(\Morus\FasBundle\Entity\UnitParts $unitParts)
    {
        $this->unitParts->removeElement($unitParts);
    }

    /**
     * Get unitParts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUnitParts()
    {
        return $this->unitParts;
    }
}
