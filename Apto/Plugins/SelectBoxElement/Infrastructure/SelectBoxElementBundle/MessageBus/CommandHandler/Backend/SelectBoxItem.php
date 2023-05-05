<?php

namespace Apto\Plugins\SelectBoxElement\Infrastructure\SelectBoxElementBundle\MessageBus\CommandHandler\Backend;

use Apto\Plugins\SelectBoxElement\Application\Backend\Commands\SelectBoxItem\SelectBoxItemCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class SelectBoxItem extends SelectBoxItemCommandHandler implements MessageSubscriberInterface
{

}