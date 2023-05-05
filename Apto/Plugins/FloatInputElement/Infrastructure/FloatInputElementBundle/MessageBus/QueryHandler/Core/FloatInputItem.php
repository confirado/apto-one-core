<?php

namespace Apto\Plugins\FloatInputElement\Infrastructure\FloatInputElementBundle\MessageBus\QueryHandler\Core;

use Apto\Plugins\FloatInputElement\Application\Core\Query\FloatInputItem\FloatInputItemQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class FloatInputItem extends FloatInputItemQueryHandler implements MessageSubscriberInterface
{

}