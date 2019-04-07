<?php


namespace App\EventSubscriber;


use App\Entity\Peacher\Peacher;
use App\Service\AvatarService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class AvatarSubscriber
 *
 * Check in Request to see if it must generate an avatar for current user
 *
 * @package App\EventSubscriber
 */
class AvatarSubscriber implements EventSubscriberInterface
{
    /** @var AvatarService */
    private $avatarService;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * AvatarSubscriber constructor.
     * @param AvatarService $avatarService
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(AvatarService $avatarService, TokenStorageInterface $tokenStorage)
    {
        $this->avatarService = $avatarService;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        // check to see if username has been filled for the first time
        if (!$event->getRequest()->attributes->get('must_generate_avatar')) {
            return;
        }

        /** @var Peacher $peacher */
        $peacher = $this->tokenStorage->getToken()->getUser();
        $this->avatarService->generateAvatar($peacher);
        $event->getRequest()->attributes->remove('must_generate_avatar');
    }
}
