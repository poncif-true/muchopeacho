<?php
namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

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
    public function setInsertDate()
    {
        $this->insertDate = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDate(): \DateTime
    {
        return $this->updateDate;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setUpdateDate()
    {
        $this->updateDate = new \DateTime();
    }
}
