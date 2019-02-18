<?php

namespace App\Service\Security;

use App\Entity\Peacher\Peacher;
use App\Exception\SecurityException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provides methods for authentication, registration, etc
 */
class SecurityService
{
    const PASSWORD_MIN_LENGTH = 8;
    const PASSWORD_MAX_LENGTH = 350;
    /**
     * At least one number, one capital, one letter
     */
    const PASSWORD_REGEXP = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/';

    /**
     * @var UserPasswordEncoder
     */
    protected $passwordEncoder;
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * SecurityService constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    /**
     *
     * @param Peacher $peacher
     * @throws SecurityException
     * @throws \Exception
     */
    public function addUser(Peacher $peacher)
    {
        $peacher = $this->setInitialFields($peacher);
        $this->entityManager->persist($peacher);
        $this->entityManager->flush();
    }

    /**
     *
     * @param Peacher $peacher
     * @return Peacher
     * @throws SecurityException
     * @throws \Exception
     */
    protected function setInitialFields(Peacher $peacher)
    {
        $peacher->setActive(true);
        $peacher->setUsername($this->getUniqueUsername());
        $password = $this->encodePassword($peacher, $peacher->getPlainPassword());
        $peacher->setPassword($password);

        return $peacher;
    }

    /**
     * Return a "unique" technical username
     * @return string
     */
    protected function getUniqueUsername()
    {
        return uniqid('peacher_', true);
    }

    /**
     * @param string $plainPassword
     * @throws SecurityException
     */
    protected function checkPasswordPattern(string $plainPassword)
    {
        if (strlen($plainPassword) < self::PASSWORD_MIN_LENGTH) {
            throw new SecurityException('Password is too short');
        }
        if (strlen($plainPassword) > self::PASSWORD_MAX_LENGTH) {
            throw new SecurityException('Password is too long');
        }
        if (!preg_match(self::PASSWORD_REGEXP, $plainPassword)) {
            throw new SecurityException('Password doesn\'t match the required pattern');
        }
    }

    /**
     * @param UserInterface $user
     * @param string $plainPassword
     * @return string
     * @throws SecurityException
     */
    protected function encodePassword(UserInterface $user, string $plainPassword): string
    {
        $this->checkPasswordPattern($plainPassword);

        return $this->passwordEncoder->encodePassword($user, $plainPassword);
    }
}
