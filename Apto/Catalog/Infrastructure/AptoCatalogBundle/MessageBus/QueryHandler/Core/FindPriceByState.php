<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\Configuration\FindPriceByStateHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class FindPriceByState extends FindPriceByStateHandler implements MessageSubscriberInterface
{

}