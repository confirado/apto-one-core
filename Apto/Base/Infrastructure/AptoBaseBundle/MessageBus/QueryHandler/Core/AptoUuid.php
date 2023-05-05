<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Core;

use Apto\Base\Application\Core\Query\AptoUuid\GenerateAptoUuidHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class AptoUuid extends GenerateAptoUuidHandler implements MessageSubscriberInterface
{

}