<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Core;

use Apto\Catalog\Application\Backend\Commands\Product\Condition\ProductConditionHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ProductCondition extends ProductConditionHandler implements MessageSubscriberInterface
{

}
