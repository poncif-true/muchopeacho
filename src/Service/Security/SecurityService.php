<?php

namespace App\Service\Security;

use App\Exception\SecurityException;
use App\Service\Security\SecurityService;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provides methods for authentication, registration, etc
 */
class SecurityService
{
    const PASSWORD_REGEXP = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/';
    const PASSWORD_MIN_LENGTH = 8;
    const PASSWORD_MAX_LENGTH = 2048;

    /** UserPasswordEncoderInterface $passwordEncoder */
    protected $passwordEncoder;

    /**
     *
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @throws SecurityException
     */
    public function checkPasswordPattern(string $plainPassword)
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

    public function encodePassword(UserInterface $user, string $plainPassword): string
    {
        $this->checkPasswordPattern($plainPassword);

        return $this->passwordEncoder->encodePassword($user, $plainPassword);
    }
}
