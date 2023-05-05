<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\MessageBus\CommandHandler\Core;

use Apto\Plugins\MaterialPickerElement\Application\Core\Commands\Material\MaterialCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Material extends MaterialCommandHandler implements MessageSubscriberInterface
{

}