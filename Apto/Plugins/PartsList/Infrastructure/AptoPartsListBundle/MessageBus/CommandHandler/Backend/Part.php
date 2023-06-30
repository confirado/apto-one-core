<?php

namespace Apto\Plugins\PartsList\Infrastructure\AptoPartsListBundle\MessageBus\CommandHandler\Backend;

use Apto\Plugins\PartsList\Application\Backend\Commands\Part\PartCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Part extends PartCommandHandler implements MessageSubscriberInterface
{

}