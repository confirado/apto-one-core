<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\Product\Element\ProductElementGalleryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ProductElementGallery extends ProductElementGalleryHandler implements MessageSubscriberInterface
{

}