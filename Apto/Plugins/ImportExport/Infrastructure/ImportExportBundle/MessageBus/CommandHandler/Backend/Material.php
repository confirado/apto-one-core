<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\MessageBus\CommandHandler\Backend;

use Apto\Plugins\ImportExport\Application\Backend\Commands\Import\MaterialPicker\MaterialCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Material extends MaterialCommandHandler implements MessageSubscriberInterface
{

}