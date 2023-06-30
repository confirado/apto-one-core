<?php

namespace Apto\Plugins\PartsList\Infrastructure\AptoPartsListBundle\MessageBus\CommandHandler\Backend;

use Apto\Plugins\PartsList\Application\Backend\Commands\Unit\UnitCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Unit extends UnitCommandHandler implements MessageSubscriberInterface
{

}