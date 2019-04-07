<?php
namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait DatabaseDatesTrait
 *
 * Provides date fields to manage database : insert and update date. Both are automatically filled with
 * Doctrine ORM's PrePersist and PreUpdate methods.
 *
 * @package App\Traits
 */
trait DatabaseDatesTrait
{
    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    protected $insertDate;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    protected $updateDate;

    /**
     * @return \DateTime
     */
    public function getInsertDate(): \DateTime
    {
        return $this->insertDate;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setInsertDate(): self
    {
        $this->insertDate = new \DateTime();

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDate(): \DateTime
    {
        return $this->updateDate;
    }

    /**
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function setUpdateDate(): self
    {
        $this->updateDate = new \DateTime();

        return $this;
    }
}
