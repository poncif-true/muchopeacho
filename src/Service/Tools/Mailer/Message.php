<?php


namespace App\Service\Tools\Mailer;


/**
 * Class Message
 * @package App\Service\Tools\Mailer
 */
class Message
{
    /** @var string */
    protected $subject;
    /** @var string */
    protected $body;
    /** @var string */
    protected $to;
    /** @var array */
    protected $from;
    /** @var array */
    protected $replyTo;
    /** @var string */
    protected $cc;

    /** @var \Swift_Message */
    protected $message;

    /** @var array */
    protected $requiredOptions = ['subject', 'body', 'from', 'to'];
    /** @var array */
    protected $missingOptions = [];

    /**
     * Message constructor.
     * @param string|null $subject
     */
    public function __construct(string $subject = null)
    {
        $this->from = [getenv('NOTIFICATION_EMAIL_ADDRESS') => 'MuchoPeacho'];
        $this->message = new \Swift_Message();
        $this->subject = $subject;
    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return Message
     */
    public function setSubject(string $subject): Message
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return Message
     */
    public function setBody(string $body): Message
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTo(): ?string
    {
        return $this->to;
    }

    /**
     * @param string $to
     * @return Message
     */
    public function setTo(string $to): Message
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getFrom(): ?array
    {
        return $this->from;
    }

    /**
     * @param array $from
     * @return Message
     */
    public function setFrom(array $from): Message
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return array
     */
    public function getMissingOptions(): array
    {
        return $this->missingOptions;
    }

    /**
     * @return \Swift_Message
     * @throws IncompleteMessageException
     */
    public function prepareMessage(): \Swift_Message
    {
        if (!$this->isMessageReady()) {
            throw new IncompleteMessageException('Missing options: ' . implode(', ', $this->missingOptions));
        }
        if (is_array($this->from) && count($this->from) === 1) {
            $this->message->setFrom(key($this->from), reset($this->from));
        } else {
            $this->message->setFrom($this->from);
        }

        $this->message->addTo($this->to);
        $this->message->setSubject($this->subject);
        $this->message->setBody($this->body);
        if (!empty($this->replyTo)) {
            $this->message->setReplyTo($this->replyTo);
        }

        return $this->message;
    }

    /**
     * @return bool
     */
    protected function isMessageReady(): bool
    {
        $missingOptions = array_filter($this->requiredOptions, function ($option) {
            return empty($this->$option);
        });

        if (!empty($missingOptions)) {
            $this->missingOptions = $missingOptions;

            return false;
        }

        return true;
    }
}
