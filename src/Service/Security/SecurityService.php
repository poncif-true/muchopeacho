<?php

namespace App\Service\Security;

use App\Entity\Peacher\Peacher;
use App\Entity\Token;
use App\Exception\SecurityException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provides methods for authentication, registration, etc
 * Class SecurityService
 * @package App\Service\Security
 */
class SecurityService
{
    const PASSWORD_MIN_LENGTH = 8;
    const PASSWORD_MAX_LENGTH = 350;
    /**
     * At least one number, one capital, one letter
     */
    const PASSWORD_REGEXP = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/';

    /** @var UserPasswordEncoder */
    protected $passwordEncoder;
    /** @var EntityManager */
    protected $entityManager;
    /** @var Producer */
    protected $producer;
    /** @var LoggerInterface $logger */
    protected $logger;

    /**
     * SecurityService constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     * @param Producer $producer
     * @param LoggerInterface $logger
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        Producer $producer,
        LoggerInterface $logger
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $producer->setLogger($logger);
        $this->producer = $producer;
        $this->logger = $logger;
    }

    /**
     * @param Peacher $peacher
     * @return Peacher
     * @throws \Exception
     */
    public function addUser(Peacher $peacher)
    {
        $peacher = $this->setInitialFields($peacher);
        $this->entityManager->persist($peacher);
        $this->entityManager->flush();

        return $peacher;
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
        $peacher->setActive(false);
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

    /**
     * @param string $email
     */
    public function sendSignUpConfirmation(string $email)
    {
        $msg = [
            'id' => uniqid('amqp_msg.notify_user.'),
            'email' => $email,
        ];
        $this->logger->info('sending a confirmation, message id: ' . $msg['id']);
        /** notify_user_producer */
        $this->producer->setContentType('application/json')->publish(json_encode($msg));
    }

    /**
     * @param Token $token
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     * @throws \UnexpectedValueException
     */
    public function confirmSignUp(Token $token)
    {
        // TODO child class ConfirmationToken that extends Token
        if ($token->getType() !== Token::TYPE_SIGN_UP_CONFIRMATION) {
            throw new \UnexpectedValueException('Confirmation token was expected');
        }
        if ($token->isAcquitted()) {
            throw new \Exception('Already acquitted');
        }
        if (new \DateTime() > $token->getExpirationDate()) {
            throw new \Exception('Token has expired');
        }
        $peacher = $token->getUser();
        $peacher->setActive(true);
        $token->setAcquitted(true);
        $this->entityManager->flush();
    }
}
