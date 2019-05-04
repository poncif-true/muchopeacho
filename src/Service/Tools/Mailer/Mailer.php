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

    protected $message;

    protected $options;
    /**
     * @var LoggerInterface $logger
     */
    protected $logger;


    public function __construct(LoggerInterface $logger, array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
        $this->logger = $logger;
        $this->mailer = $this->getMailer();
    }

    protected function getMailer()
    {
        $transport = new \Swift_SmtpTransport($this->options['host'], $this->options['port'], 'ssl');
        $transport->setUsername($this->options['username'])
            ->setPassword($this->options['password']);

        return new \Swift_Mailer($transport);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'host'       => getenv('SMTP_HOST'),
            'username'   => getenv('SMTP_USER'),
            'password'   => getenv('SMTP_PWD'),
            'port'       => getenv('SMTP_PORT'),
        ]);
    }

    public function send(Message $message)
    {

        return $this->mailer->send($message->prepareMessage());
    }
}
