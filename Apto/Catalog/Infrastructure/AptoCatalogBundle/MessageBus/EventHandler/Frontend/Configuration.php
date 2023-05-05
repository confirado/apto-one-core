<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\EventHandler\Frontend;

use Apto\Catalog\Application\Frontend\Subscribers\Configuration\ConfigurationEventHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Configuration extends ConfigurationEventHandler implements MessageSubscriberInterface
{

}