<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\Product\ComputedProductValue\ComputedProductValueHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ComputedProductValue extends ComputedProductValueHandler implements MessageSubscriberInterface
{

}