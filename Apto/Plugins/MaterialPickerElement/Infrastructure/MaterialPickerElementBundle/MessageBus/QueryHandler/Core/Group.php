<?php
namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\MessageBus\QueryHandler\Core;

use Apto\Plugins\MaterialPickerElement\Application\Core\Query\Property\GroupQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Group extends GroupQueryHandler implements MessageSubscriberInterface
{

}