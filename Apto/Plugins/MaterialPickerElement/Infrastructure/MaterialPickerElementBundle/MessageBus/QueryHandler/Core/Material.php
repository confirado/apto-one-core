<?php
namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\MessageBus\QueryHandler\Core;

use Apto\Plugins\MaterialPickerElement\Application\Core\Query\Material\MaterialQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Material extends MaterialQueryHandler implements MessageSubscriberInterface
{

}