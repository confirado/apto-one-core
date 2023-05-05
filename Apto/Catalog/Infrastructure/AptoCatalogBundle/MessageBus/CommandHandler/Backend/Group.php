<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\Group\GroupCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Group extends GroupCommandHandler implements MessageSubscriberInterface
{

}