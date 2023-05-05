<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\MessageBus\CommandHandler\Backend;

use Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material\MaterialCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Material extends MaterialCommandHandler implements MessageSubscriberInterface
{

}