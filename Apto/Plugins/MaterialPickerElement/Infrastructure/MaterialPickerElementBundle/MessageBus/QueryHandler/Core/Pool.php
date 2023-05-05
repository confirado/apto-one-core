<?php
namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\MessageBus\QueryHandler\Core;

use Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool\PoolQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Pool extends PoolQueryHandler implements MessageSubscriberInterface
{

}