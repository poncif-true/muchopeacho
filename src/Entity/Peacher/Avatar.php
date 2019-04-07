<?php

namespace App\Entity\Peacher;

use App\Traits\DatabaseDatesTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Peacher\AvatarRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Avatar
{
    use DatabaseDatesTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $filename;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Peacher\Peacher", inversedBy="avatar", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $peacher;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getPeacher(): ?Peacher
    {
        return $this->peacher;
    }

    public function setPeacher(Peacher $peacher): self
    {
        $this->peacher = $peacher;

        return $this;
    }
}
