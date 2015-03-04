<?php

namespace Morus\FasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Export
 *
 * @ORM\Table(name="fas_export")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Export
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=true)
     */
    protected $sortOrder;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    protected $createDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_modified_date", type="datetime", nullable=true)
     */
    protected $lastModifiedDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="inactive_date", type="datetime", nullable=true)
     */
    protected $inactiveDate;

    /**
     * @ORM\OneToMany(targetEntity="Statement", mappedBy="export", cascade={"persist"})
     **/
    protected $statements;

    /**
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="export", cascade={"persist"})
     **/
    protected $transactions;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->statements = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     * @return Export
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder
     *
     * @return integer 
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Export
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Export
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set lastModifiedDate
     *
     * @param \DateTime $lastModifiedDate
     * @return Export
     */
    public function setLastModifiedDate($lastModifiedDate)
    {
        $this->lastModifiedDate = $lastModifiedDate;

        return $this;
    }

    /**
     * Get lastModifiedDate
     *
     * @return \DateTime 
     */
    public function getLastModifiedDate()
    {
        return $this->lastModifiedDate;
    }

    /**
     * Set inactiveDate
     *
     * @param \DateTime $inactiveDate
     * @return Export
     */
    public function setInactiveDate($inactiveDate)
    {
        $this->inactiveDate = $inactiveDate;

        return $this;
    }

    /**
     * Get inactiveDate
     *
     * @return \DateTime 
     */
    public function getInactiveDate()
    {
        return $this->inactiveDate;
    }

    /**
     * Add statement
     *
     * @param \Morus\FasBundle\Entity\Statement $statement
     * @return Export
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
     * Add transactions
     *
     * @param \Morus\FasBundle\Entity\Transaction $transaction
     * @return Export
     */
    public function addTransaction(\Morus\FasBundle\Entity\Transaction $transaction)
    {
        $this->statements[] = $transaction;

        return $this;
    }

    /**
     * Remove transaction
     *
     * @param \Morus\FasBundle\Entity\Transaction $transaction
     */
    public function removeTransaction(\Morus\FasBundle\Entity\Transaction $transaction)
    {
        $this->statements->removeElement($transaction);
    }

    /**
     * Get statements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTransactions()
    {
        return $this->statements;
    }
    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        // Add your code here
    }

    /**
     * @ORM\PostPersist
     */
    public function onPostPersist()
    {
        // Add your code here
    }
}
