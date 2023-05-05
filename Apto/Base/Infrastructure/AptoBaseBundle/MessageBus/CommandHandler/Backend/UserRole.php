<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Backend;

use Apto\Base\Application\Backend\Commands\UserRole\UserRoleCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class UserRole extends UserRoleCommandHandler implements MessageSubscriberInterface
{

}