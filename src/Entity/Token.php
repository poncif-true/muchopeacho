<?php

namespace App\Entity;

use App\Entity\Peacher\Peacher;
use App\Traits\DatabaseDatesTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TokenRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="integer")
 * @ORM\DiscriminatorMap({1 = "SignUpConfirmationToken", 2 = "PasswordToken"})
 */
abstract class Token
{
    use DatabaseDatesTrait;

    const TYPE_SIGN_UP_CONFIRMATION = 1;
    const TYPE_PWD_RENEWAL = 2;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $value;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $expirationDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Peacher\Peacher")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $user;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $acquitted;

    /** @var bool */
    protected $expired;


    /**
     * Token constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->acquitted = false;
        $this->expirationDate = $this->getDefautExpirationDate();
    }

    /**
     * @return \DateTime
     * @throws \Exception
     */
    protected function getDefautExpirationDate()
    {
        $now = new \DateTime(); //current date/time
        $now->add(new \DateInterval("PT1H"));

        return $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getUser(): ?Peacher
    {
        return $this->user;
    }

    public function setUser(?Peacher $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isAcquitted(): ?bool
    {
        return $this->acquitted;
    }

    public function setAcquitted(bool $acquitted): self
    {
        $this->acquitted = $acquitted;

        return $this;
    }

    public function isExpired(): bool
    {
        return $this->expired;
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkExpired()
    {
        $this->expired = (new \DateTime() > $this->getExpirationDate());
    }
}
