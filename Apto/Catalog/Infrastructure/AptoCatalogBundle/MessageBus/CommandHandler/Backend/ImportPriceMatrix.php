<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\PriceMatrix\ImportPriceMatrixHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ImportPriceMatrix extends ImportPriceMatrixHandler implements MessageSubscriberInterface
{

}