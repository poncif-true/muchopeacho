<?php


namespace App\Email;


use App\Entity\Token;
use App\Service\Tools\Mailer\AbstractEmail;
use App\Service\Tools\Mailer\EmailGenerator;
use App\Service\Tools\Mailer\Message;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class SignUpConfirmation
 * @package App\Email
 */
class SignUpConfirmation extends AbstractEmail
{
    /** @var array */
    protected static $requirements = [
        EmailGenerator::AVAILABLE_SERVICES['router'],
    ];
    /** @var array  */
    protected static $arguments = [
        'token' => Token::class,
    ];

    /**
     * @param array $args
     * @return Message
     */
    public function __invoke(array $args = []): Message
    {
        /** @var RouterInterface $router */
        /** @var Token $token */
        $arguments = $this->resolveArguments($args);
        $router = $arguments['router'];
        $token = $arguments['token'];

        $body = 'Please click on link below to confirm your inscription: ' . "\n\r"
            . $router->generate('confirm_sign_up', ['tokenValue' => $token->getValue()], Router::ABSOLUTE_URL);

        $message = $this->newMessage('Welcome to MuchoPeacho');
        $message->setTo($token->getUser()->getEmail())
            ->setBody($body);

        return $message;
    }
}
