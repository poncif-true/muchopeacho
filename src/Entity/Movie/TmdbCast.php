<?php

namespace App\Entity\Movie;

use App\Traits\DatabaseDatesTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Movie\TmdbCastRepository")
 */
class TmdbCast
{
    use DatabaseDatesTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $tmdbApiId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTmdbApiId(): ?int
    {
        return $this->tmdbApiId;
    }

    public function setTmdbApiId(int $tmdbApiId): self
    {
        $this->tmdbApiId = $tmdbApiId;

        return $this;
    }
}
