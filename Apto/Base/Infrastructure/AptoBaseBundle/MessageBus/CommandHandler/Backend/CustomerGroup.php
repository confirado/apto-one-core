<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Backend;

use Apto\Base\Application\Backend\Commands\CustomerGroup\CustomerGroupCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class CustomerGroup extends CustomerGroupCommandHandler implements MessageSubscriberInterface
{

}