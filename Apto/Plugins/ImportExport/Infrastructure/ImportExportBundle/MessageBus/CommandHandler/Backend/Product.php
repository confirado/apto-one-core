<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\MessageBus\CommandHandler\Backend;

use Apto\Plugins\ImportExport\Application\Backend\Commands\Import\Product\ProductCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Product extends ProductCommandHandler implements MessageSubscriberInterface
{

}