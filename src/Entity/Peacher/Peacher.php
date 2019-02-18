<?php

namespace App\Entity\Peacher;

use App\Traits\DatabaseDatesTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Peacher\PeacherRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Peacher implements UserInterface
{
    use DatabaseDatesTrait;

    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * Will be erased after persist, not related to database
     * @var string
     */
    private $plainPassword;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=true)
     */
    private $displayUsername;

    /**
     * @var string
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $style;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $active;

    public function __construct()
    {
        $this->movieSearches = new ArrayCollection();
        $this->usernames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
         $this->plainPassword = null;
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function setStyle(?string $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     * @return Peacher
     */
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDisplayUsername(): ?string
    {
        return $this->displayUsername;
    }

    /**
     * @param mixed $displayUsername
     * @return Peacher
     */
    public function setDisplayUsername($displayUsername)
    {
        $this->displayUsername = $displayUsername;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }

    /**
     * @param \DateTime $lastLogin
     * @return Peacher
     */
    public function setLastLogin(\DateTime $lastLogin): self
    {
        $this->lastLogin = $lastLogin;
        return $this;
    }
}
