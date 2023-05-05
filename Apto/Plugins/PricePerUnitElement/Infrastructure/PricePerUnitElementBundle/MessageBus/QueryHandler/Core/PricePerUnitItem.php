<?php

namespace Apto\Plugins\PricePerUnitElement\Infrastructure\PricePerUnitElementBundle\MessageBus\QueryHandler\Core;

use Apto\Plugins\PricePerUnitElement\Application\Core\Query\PricePerUnitItem\PricePerUnitItemQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class PricePerUnitItem extends PricePerUnitItemQueryHandler implements MessageSubscriberInterface
{

}