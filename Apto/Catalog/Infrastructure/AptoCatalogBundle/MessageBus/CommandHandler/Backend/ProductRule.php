<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\Product\Rule\ProductRuleHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ProductRule extends ProductRuleHandler implements MessageSubscriberInterface
{

}