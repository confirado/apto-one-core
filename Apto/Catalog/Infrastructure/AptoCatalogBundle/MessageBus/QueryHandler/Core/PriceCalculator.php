<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\PriceCalculator\PriceCalculatorHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class PriceCalculator extends PriceCalculatorHandler implements MessageSubscriberInterface
{

}