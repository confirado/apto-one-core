<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\PriceMatrix\PriceMatrixCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class PriceMatrix extends PriceMatrixCommandHandler implements MessageSubscriberInterface
{

}