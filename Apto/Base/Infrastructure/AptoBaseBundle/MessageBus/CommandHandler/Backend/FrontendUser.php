<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Backend;

use Apto\Base\Application\Backend\Commands\FrontendUser\FrontendUserCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class FrontendUser extends FrontendUserCommandHandler implements MessageSubscriberInterface
{
}
