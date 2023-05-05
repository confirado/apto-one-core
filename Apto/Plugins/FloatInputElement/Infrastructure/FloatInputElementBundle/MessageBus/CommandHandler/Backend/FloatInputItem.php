<?php

namespace Apto\Plugins\FloatInputElement\Infrastructure\FloatInputElementBundle\MessageBus\CommandHandler\Backend;

use Apto\Plugins\FloatInputElement\Application\Backend\Commands\FloatInputItem\FloatInputItemCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class FloatInputItem extends FloatInputItemCommandHandler implements MessageSubscriberInterface
{

}