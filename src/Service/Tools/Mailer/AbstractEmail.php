<?php


namespace App\Service\Tools\Mailer;



use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractEmail
{
    protected static $requirements = [];
    protected static $arguments = [];

    /**
     * @return array
     */
    public static function getRequirements(): array
    {
        return static::$requirements;
    }

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

    abstract public function __invoke(array $args = []): Message;
}
