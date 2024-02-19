<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\MessageBus\CommandHandler\Backend;

use Apto\Plugins\ImportExport\Application\Backend\Commands\Import\Rule\RuleCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Rule extends RuleCommandHandler implements MessageSubscriberInterface
{

}