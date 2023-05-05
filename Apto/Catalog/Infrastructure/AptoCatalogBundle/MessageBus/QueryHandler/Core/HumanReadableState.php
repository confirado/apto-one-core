<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\Configuration\FindHumanReadableStateHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class HumanReadableState extends FindHumanReadableStateHandler implements MessageSubscriberInterface
{

}