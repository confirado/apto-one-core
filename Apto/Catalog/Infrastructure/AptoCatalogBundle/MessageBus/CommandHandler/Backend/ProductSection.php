<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\Product\Section\ProductSectionHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ProductSection extends ProductSectionHandler implements MessageSubscriberInterface
{

}