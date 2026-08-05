<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Apto\Catalog\Application\Core\Query\Configuration\FindSubstitutesByStateHandler;

class FindSubstitutesByState extends FindSubstitutesByStateHandler implements MessageSubscriberInterface
{

}
