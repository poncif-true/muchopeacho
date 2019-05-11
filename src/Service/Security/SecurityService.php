<?php

namespace App\Service\Security;

use App\Entity\PasswordToken;
use App\Entity\Peacher\Peacher;
use App\Entity\SignUpConfirmationToken;
use App\Exception\SecurityException;
use App\Repository\Peacher\PeacherRepository;
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
 *
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
    /** @var PeacherRepository */
    protected $peacherRepository;
    /** @var Producer */
    protected $confirmSignUpProducer;
    /** @var Producer */
    protected $resetPasswordProducer;
    /** @var LoggerInterface $logger */
    protected $logger;

    /**
     * SecurityService constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     * @param Producer $confirmSignUpProducer
     * @param Producer $resetPasswordProducer
     * @param LoggerInterface $logger
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        Producer $confirmSignUpProducer,
        Producer $resetPasswordProducer,
        LoggerInterface $logger
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->peacherRepository = $entityManager->getRepository(Peacher::class);
        $confirmSignUpProducer->setLogger($logger);
        $this->confirmSignUpProducer = $confirmSignUpProducer;
        $resetPasswordProducer->setLogger($logger);
        $this->resetPasswordProducer = $resetPasswordProducer;
        $this->logger = $logger;
    }

    /**
     * @param Peacher $peacher
     *
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
     *
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
     *
     * @return string
     */
    protected function getUniqueUsername()
    {
        return uniqid('peacher_', true);
    }

    /**
     * @param UserInterface $user
     * @param string $plainPassword
     *
     * @return string
     * @throws SecurityException
     */
    protected function encodePassword(UserInterface $user, string $plainPassword): string
    {
        $this->checkPasswordPattern($plainPassword);

        return $this->passwordEncoder->encodePassword($user, $plainPassword);
    }

    /**
     * @param string $plainPassword
     *
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
     * @param string $email
     */
    public function sendSignUpConfirmation(string $email)
    {
        $msg = [
            'id'    => uniqid('amqp_msg.confirm_sign_up.'),
            'email' => $email,
        ];
        $this->logger->info('publishing for confirmation, message id: ' . $msg['id']);
        /** confirm_sign_up */
        $this->confirmSignUpProducer->setContentType('application/json')->publish(json_encode($msg));
    }

    /**
     * Publish a AMQP message to send an email to user
     *
     * @param string $email The email of the user that wants to reset his password
     *
     * @throws \UnexpectedValueException If an active account cannot be found
     */
    public function sendResetPasswordEmail(string $email)
    {
        $peacher = $this->peacherRepository->findOneBy(['email' => $email]);

        if (!$peacher || !$peacher->isActive()) {
            throw new \UnexpectedValueException('Account not found, or it has been deactivated');
        }

        $msg = [
            'id'    => uniqid('amqp_msg.reset_password.'),
            'email' => $email,
        ];
        $this->logger->info('publishing for password renewal, message id: ' . $msg['id']);
        /** reset_password */
        $this->resetPasswordProducer->setContentType('application/json')->publish(json_encode($msg));
    }

    /**
     * @param SignUpConfirmationToken $token
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     * @throws \UnexpectedValueException
     */
    public function confirmSignUp(SignUpConfirmationToken $token)
    {
        if ($token->isAcquitted()) {
            throw new \Exception('Already acquitted');
        }
        if ($token->isExpired()) {
            throw new \Exception('Token has expired');
        }
        $peacher = $token->getUser();
        $peacher->setActive(true);
        $token->setAcquitted(true);
        $this->entityManager->flush();
    }

    /**
     * @param PasswordToken $token
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     * @throws \UnexpectedValueException
     */
    public function resetPassword(PasswordToken $token)
    {
        if ($token->isAcquitted()) {
            throw new \Exception('Already acquitted');
        }
        if ($token->isExpired()) {
            throw new \Exception('Token has expired');
        }
        $peacher = $token->getUser();
        $password = $this->encodePassword($peacher, $peacher->getPlainPassword());
        $peacher->setPassword($password);
        $token->setAcquitted(true);
        $this->entityManager->flush();
    }
}
