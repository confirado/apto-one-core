<?php
namespace Apto\Plugins\PartsList\Infrastructure\AptoPartsListBundle\MessageBus\QueryHandler\Core;

use Apto\Plugins\PartsList\Application\Core\Query\Unit\UnitQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Unit extends UnitQueryHandler implements MessageSubscriberInterface
{

}