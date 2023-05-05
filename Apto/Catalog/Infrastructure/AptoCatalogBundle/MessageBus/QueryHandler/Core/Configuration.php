<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\Configuration\ConfigurationQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Configuration extends ConfigurationQueryHandler implements MessageSubscriberInterface
{

}