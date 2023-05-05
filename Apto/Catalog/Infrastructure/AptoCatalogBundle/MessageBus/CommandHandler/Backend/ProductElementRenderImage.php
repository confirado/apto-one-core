<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\Product\Element\ProductElementRenderImageHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ProductElementRenderImage extends ProductElementRenderImageHandler implements MessageSubscriberInterface
{

}