<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\MessageBus\CommandHandler\Backend;

use Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\PriceGroup\PriceGroupCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class PriceGroup extends PriceGroupCommandHandler implements MessageSubscriberInterface
{

}