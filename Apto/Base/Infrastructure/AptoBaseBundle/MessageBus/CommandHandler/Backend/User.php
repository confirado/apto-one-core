<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Backend;

use Apto\Base\Application\Backend\Commands\User\UserCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class User extends UserCommandHandler implements MessageSubscriberInterface
{

}