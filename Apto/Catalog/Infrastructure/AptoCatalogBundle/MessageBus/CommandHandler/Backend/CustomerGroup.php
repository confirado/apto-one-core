<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\CustomerGroup\CustomerGroupCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class CustomerGroup extends CustomerGroupCommandHandler implements MessageSubscriberInterface
{

}