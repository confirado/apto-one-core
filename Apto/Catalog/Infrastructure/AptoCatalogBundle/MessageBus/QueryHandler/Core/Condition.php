<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\Product\Condition\ConditionQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Condition extends ConditionQueryHandler implements MessageSubscriberInterface
{

}
