<?php
namespace Apto\Plugins\PartsList\Infrastructure\AptoPartsListBundle\MessageBus\QueryHandler\Core;

use Apto\Plugins\PartsList\Application\Core\Query\Part\PartQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Part extends PartQueryHandler implements MessageSubscriberInterface
{

}