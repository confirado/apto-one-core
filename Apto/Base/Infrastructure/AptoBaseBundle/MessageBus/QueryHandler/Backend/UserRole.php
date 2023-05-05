<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Backend;

use Apto\Base\Application\Backend\Query\UserRole\UserRoleQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class UserRole extends UserRoleQueryHandler implements MessageSubscriberInterface
{

}