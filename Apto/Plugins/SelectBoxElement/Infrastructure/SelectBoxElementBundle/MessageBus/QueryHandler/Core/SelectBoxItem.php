<?php

namespace Apto\Plugins\SelectBoxElement\Infrastructure\SelectBoxElementBundle\MessageBus\QueryHandler\Core;

use Apto\Plugins\SelectBoxElement\Application\Core\Query\SelectBoxItem\SelectBoxItemQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class SelectBoxItem extends SelectBoxItemQueryHandler implements MessageSubscriberInterface
{

}