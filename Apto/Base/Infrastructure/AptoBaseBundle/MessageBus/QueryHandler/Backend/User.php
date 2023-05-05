<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Backend;

use Apto\Base\Application\Backend\Query\User\UserQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class User extends UserQueryHandler implements MessageSubscriberInterface
{

}