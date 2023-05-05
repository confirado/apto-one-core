<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\Filter\FilterPropertyQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class FilterProperty extends FilterPropertyQueryHandler implements MessageSubscriberInterface
{

}