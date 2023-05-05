<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\Product\Rule\RuleQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Rule extends RuleQueryHandler implements MessageSubscriberInterface
{

}