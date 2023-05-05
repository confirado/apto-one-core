<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Frontend;

use Apto\Catalog\Application\Frontend\Commands\Configuration\ConfigurationCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Configuration extends ConfigurationCommandHandler implements MessageSubscriberInterface
{

}