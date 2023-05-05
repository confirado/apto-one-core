<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\MessageBus\CommandHandler\Backend;

use Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Pool\PoolCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Pool extends PoolCommandHandler implements MessageSubscriberInterface
{

}