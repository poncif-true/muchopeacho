<?php


namespace App\Service\Tools\Mailer;


use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractEmail
 * @package App\Service\Tools\Mailer
 */
abstract class AbstractEmail
{
    /** @var array */
    protected static $arguments = [];
    /** @var array */
    protected static $requirements = [];

    /**
     * @return array
     */
    public static function getRequirements(): array
    {
        return static::$requirements;
    }

    /**
     * @param array $args
     * @return array
     */
    protected function resolveArguments(array $args = []): array
    {
        $resolver = new OptionsResolver();
        foreach (static::$arguments as $arg => $type) {
            $resolver->setDefault($arg, null);
            $resolver->setRequired($arg);
            $resolver->setAllowedTypes($arg, $type);
        }
        foreach (static::$requirements as $requirement) {
            $resolver->setDefault($requirement, null);
            $resolver->setRequired($requirement);
        }

        return $resolver->resolve($args);
    }

    /**
     * @param array $args
     * @return Message
     */
    abstract public function __invoke(array $args = []): Message;
}
