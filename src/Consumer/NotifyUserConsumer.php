<?php

namespace App\Consumer;


use App\Service\TokenService;
use App\Service\Tools\Mailer\EmailGenerator;
use App\Service\Tools\Mailer\Mailer;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

/**
 * Class NotifyUserConsumer
 * @package App\Consumer
 */
class NotifyUserConsumer implements ConsumerInterface
{
    /** @var Mailer $mailer */
    protected $mailer;
    /** @var EmailGenerator $emailGenerator */
    protected $emailGenerator;
    /** @var TokenService $tokenService */
    protected $tokenService;
    /** @var LoggerInterface $logger */
    protected $logger;

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
        TokenService $tokenService,
        LoggerInterface $logger
    ) {
        $this->mailer = $mailer;
        $this->emailGenerator = $emailGenerator;
        $this->tokenService = $tokenService;
        $this->logger = $logger;
    }

    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        $body = json_decode($msg->body);
        $email = $body->email;
        $this->logger->info('Message received: ' . $body->id);

        try {
            $token = $this->tokenService->generateConfirmationToken($email);
            $this->logger->debug('Token generated with ID: ' . $token->getId());
            $message = $this->emailGenerator->getMessage('App\Email\SignUpConfirmation', ['token' => $token]);
            $isSendingSuccess = $this->mailer->send($message);
        } catch (\Exception $exception) {
            $this->logger->error('Unable to deliver email (message id ' . $body->id . '): ' . $exception->getMessage());
        }

        if (empty($isSendingSuccess)) {
            return false;
        }

        return true;
    }
}
