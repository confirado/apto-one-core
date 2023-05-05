<?php
namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\MessageBus\QueryHandler\Core;

use Apto\Plugins\MaterialPickerElement\Application\Core\Query\PriceGroup\PriceGroupQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class PriceGroup extends PriceGroupQueryHandler implements MessageSubscriberInterface
{
    
}