<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\Product\Condition\ProductConditionHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Condition extends ProductConditionHandler implements MessageSubscriberInterface
{

}
