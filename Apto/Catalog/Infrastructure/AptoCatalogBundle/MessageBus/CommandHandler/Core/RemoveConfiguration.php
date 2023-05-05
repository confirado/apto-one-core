<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Core;

use Apto\Catalog\Application\Core\Commands\Configuration\RemoveConfigurationHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class RemoveConfiguration extends RemoveConfigurationHandler implements MessageSubscriberInterface
{

}