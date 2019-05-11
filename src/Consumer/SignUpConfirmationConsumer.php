<?php

namespace App\Consumer;


use App\Service\TokenService;
use App\Service\Tools\Mailer\EmailGenerator;
use App\Service\Tools\Mailer\Mailer;
use App\Service\Tools\Mailer\Message;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

/**
 * Class SignUpConfirmationConsumer
 * @package App\Consumer
 */
class SignUpConfirmationConsumer extends NotifyUserConsumer
{
    /** @var TokenService $tokenService */
    protected $tokenService;

    /**
     * NotifyUserConsumer constructor.
     * @param Mailer $mailer
     * @param EmailGenerator $emailGenerator
     * @param TokenService $tokenService
     * @param LoggerInterface $logger
     */
    public function __construct(
        Mailer $mailer,
        EmailGenerator $emailGenerator,
        LoggerInterface $logger,
        TokenService $tokenService
    ) {
        parent::__construct($mailer, $emailGenerator, $logger);
        $this->tokenService = $tokenService;
    }

    /**
     * @param mixed $body
     * @return Message
     * @throws \Exception
     */
    protected function getMessage($body): Message
    {
        var_dump($body);
        $email = $body->email;
        $token = $this->tokenService->generateToken($email, 'App\Entity\SignUpConfirmationToken');
        $this->logger->info('Token generated with ID: ' . $token->getId());

        return $this->emailGenerator->getMessage('App\Email\SignUpConfirmationEmail', ['token' => $token]);
    }
}
