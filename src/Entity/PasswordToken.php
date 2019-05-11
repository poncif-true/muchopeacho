<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class PasswordToken extends Token
{
    // Override parent::DEFAULT_VALIDITY_TIME to change default validity time
}
