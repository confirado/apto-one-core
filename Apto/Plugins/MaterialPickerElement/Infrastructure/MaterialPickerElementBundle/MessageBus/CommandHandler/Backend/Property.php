<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\MessageBus\CommandHandler\Backend;

use Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Property\PropertyCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Property extends PropertyCommandHandler implements MessageSubscriberInterface
{

}