<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\Product\ProductCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Product extends ProductCommandHandler implements MessageSubscriberInterface
{

}