<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Frontend;

use Apto\Catalog\Application\Frontend\Query\Configuration\ConfigurationStateQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Configuration extends ConfigurationStateQueryHandler implements MessageSubscriberInterface
{

}