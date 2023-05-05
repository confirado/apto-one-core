<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\Product\Element\ProductElementHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ProductElement extends ProductElementHandler implements MessageSubscriberInterface
{

}