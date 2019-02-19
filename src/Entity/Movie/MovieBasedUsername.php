<?php

namespace App\Entity\Movie;

use App\Entity\Peacher\Peacher;
use App\Traits\DatabaseDatesTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Movie\MovieBasedUsernameRepository")
 */
class MovieBasedUsername
{
    use DatabaseDatesTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Movie\TmdbMovie", inversedBy="movieBasedUsernames")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tmdbMovie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Movie\TmdbCast")
     */
    private $tmdbCast;

    /**
     * @ORM\OneToOne(
     *     targetEntity="App\Entity\Peacher\Peacher",
     *     inversedBy="movieBasedUsername",
     *     cascade={"persist", "remove"}
     *     )
     */
    private $peacher;

    /**
     * @ORM\Column(type="boolean")
     */
    private $custom;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTmdbMovie(): ?TmdbMovie
    {
        return $this->tmdbMovie;
    }

    public function setTmdbMovie(?TmdbMovie $tmdbMovie): self
    {
        $this->tmdbMovie = $tmdbMovie;

        return $this;
    }

    public function getTmdbCast(): ?TmdbCast
    {
        return $this->tmdbCast;
    }

    public function setTmdbCast(?TmdbCast $tmdbCast): self
    {
        $this->tmdbCast = $tmdbCast;

        return $this;
    }

    public function getPeacher(): ?Peacher
    {
        return $this->peacher;
    }

    public function setPeacher(?Peacher $peacher): self
    {
        $this->peacher = $peacher;

        return $this;
    }

    public function isCustom(): ?bool
    {
        return $this->custom;
    }

    public function setCustom(bool $custom): self
    {
        $this->custom = $custom;

        return $this;
    }
}
