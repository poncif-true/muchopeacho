<?php


namespace App\Service\Tools\Mailer;


use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Mailer
 * @package App\Service\Tools\Mailer
 */
class Mailer
{
    /** @var \Swift_Mailer $mailer */
    protected $mailer;

    /** @var array $options */
    protected $options;

    /** @var LoggerInterface $logger */
    protected $logger;


    /**
     * Mailer constructor.
     * @param LoggerInterface $logger
     * @param array $options
     */
    public function __construct(LoggerInterface $logger, array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
        // TODO log in separate file
        $this->logger = $logger;
        $this->mailer = $this->getMailer();
    }

    /**
     * @return \Swift_Mailer
     */
    protected function getMailer(): \Swift_Mailer
    {
        $transport = new \Swift_SmtpTransport($this->options['host'], $this->options['port'], 'ssl');
        $transport->setUsername($this->options['username'])
            ->setPassword($this->options['password']);

        return new \Swift_Mailer($transport);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'host'       => getenv('SMTP_HOST'),
            'username'   => getenv('SMTP_USER'),
            'password'   => getenv('SMTP_PWD'),
            'port'       => getenv('SMTP_PORT'),
        ]);
    }

    /**
     * @param Message $message
     * @return int The number of successful recipients. Can be 0 which indicates failure
     */
    public function send(Message $message)
    {
        $this->logger->debug(
            __CLASS__ . ' sending ' . $message->getName() .
            ' to ' . var_export($message->getTo(), true)
        );
        $recipients = $this->mailer->send($message->prepareMessage());
        $this->logger->debug(
            __CLASS__ . ' successfully sent ' . $message->getName() . ' to ' . $recipients . ' recipient'
        );

        return $recipients;
    }
}
