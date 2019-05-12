<?php

namespace App\Consumer;


use App\Service\Tools\Mailer\EmailGenerator;
use App\Service\Tools\Mailer\Mailer;
use App\Service\Tools\Mailer\Message;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

/**
 * Class NotifyUserConsumer
 *
 * @package App\Consumer
 */
abstract class NotifyUserConsumer implements ConsumerInterface
{
    /** @var Mailer $mailer */
    protected $mailer;
    /** @var EmailGenerator $emailGenerator */
    protected $emailGenerator;
    /** @var LoggerInterface $logger */
    protected $logger;

    /**
     * Stores the ids of messages, for which sending was already tried once
     *
     * @var array $tries
     */
    protected $tries = [];

    /**
     * NotifyUserConsumer constructor.
     *
     * @param Mailer $mailer
     * @param EmailGenerator $emailGenerator
     * @param LoggerInterface $logger
     */
    public function __construct(
        Mailer $mailer,
        EmailGenerator $emailGenerator,
        LoggerInterface $logger
    ) {
        $this->mailer = $mailer;
        $this->emailGenerator = $emailGenerator;
        $this->logger = $logger;
    }

    /**
     * @param AMQPMessage $msg
     *
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        $body = json_decode($msg->body);
        $this->logger->info('Message received: ' . $body->id);

        try {
            $message = $this->getMessage($body);
            $isSendingSuccess = $this->mailer->send($message);
        } catch (\Exception $exception) {
            $this->logger->error(
                "Unable to deliver email (message id {$body->id}). Exception " .
                get_class($exception) . ': ' . $exception->getMessage() . '. Trace: ' . $exception->getTrace()
            );
        }

        unset($token);
        unset($message);

        if (empty($isSendingSuccess)) {
            if (!in_array($body->id, $this->tries)) {
                $this->tries[] = $body->id;
                return false;
            } else {
                $this->logger->error('message abondonned: ' . $body->id);
                unset($this->tries[$body->id]);
                return true;
            }
        }

        return true;
    }

    /**
     * @param mixed $body the decoded body from AMQP message
     *
     * @return Message
     */
    abstract protected function getMessage($body): Message;
}
