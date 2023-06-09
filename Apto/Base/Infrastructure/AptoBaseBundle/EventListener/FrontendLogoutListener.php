<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\EventListener;

use Apto\Base\Infrastructure\AptoBaseBundle\Security\FrontendUser\FrontendUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class FrontendLogoutListener implements EventSubscriberInterface
{
    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [LogoutEvent::class => 'onLogout'];
    }

    /**
     * @param LogoutEvent $event
     * @return void
     */
    public function onLogout(LogoutEvent $event): void
    {
        $token = $event->getToken();

        // get the security token of the session that is about to be logged out
        if (null === $token || $token->getUser() instanceof FrontendUser) {
            $event->setResponse(new JsonResponse([
                'isLoggedIn' => false
            ]));
        }
    }
}
