<?php


namespace App\Service\Tools\Mailer;


use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class EmailGenerator
{
    const AVAILABLE_SERVICES = [
        'router' => 'router',
    ];

    /** @var Router */
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getMessage(string $key, array $args = []): Message
    {
        if (!class_exists($key)) {
            throw new \InvalidArgumentException('The key you provided does not refer to an existing class');
        }
        $obj = new $key();
        if (!$obj instanceof AbstractEmail) {
            throw new \InvalidArgumentException('The key you provided does not match with any email');
        }
        $requirements = $obj::getRequirements();
        if (!empty($requirements)) {
            foreach ($requirements as $requirement) {
                if (!property_exists(self::class, $requirement)) {
                    throw new \LogicException('Invalid requirement: ' . $requirement);
                }
                $args[$requirement] = $this->$requirement;
            }
        }

        return $obj($args);
    }
}
