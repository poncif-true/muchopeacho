<?php

namespace App\Entity\Movie;

use App\Traits\DatabaseDatesTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Movie\TmdbMovieRepository")
 */
class TmdbMovie
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
    private $title;

    /**
     * @ORM\Column(type="integer")
     */
    private $tmdbApiId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $realeaseYear;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $directorName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Movie\MovieBasedUsername", mappedBy="tmdbMovie")
     */
    private $movieBasedUsernames;

    public function __construct()
    {
        $this->movieBasedUsernames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
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

    public function getRealeaseYear(): ?int
    {
        return $this->realeaseYear;
    }

    public function setRealeaseYear(?int $realeaseYear): self
    {
        $this->realeaseYear = $realeaseYear;

        return $this;
    }

    public function getDirectorName(): ?string
    {
        return $this->directorName;
    }

    public function setDirectorName(?string $directorName): self
    {
        $this->directorName = $directorName;

        return $this;
    }

    /**
     * @return Collection|MovieBasedUsername[]
     */
    public function getMovieBasedUsernames(): Collection
    {
        return $this->movieBasedUsernames;
    }

    public function addMovieBasedUsername(MovieBasedUsername $movieBasedUsername): self
    {
        if (!$this->movieBasedUsernames->contains($movieBasedUsername)) {
            $this->movieBasedUsernames[] = $movieBasedUsername;
            $movieBasedUsername->setTmdbMovie($this);
        }

        return $this;
    }

    public function removeMovieBasedUsername(MovieBasedUsername $movieBasedUsername): self
    {
        if ($this->movieBasedUsernames->contains($movieBasedUsername)) {
            $this->movieBasedUsernames->removeElement($movieBasedUsername);
            // set the owning side to null (unless already changed)
            if ($movieBasedUsername->getTmdbMovie() === $this) {
                $movieBasedUsername->setTmdbMovie(null);
            }
        }

        return $this;
    }
}
