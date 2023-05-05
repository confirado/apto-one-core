<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\Group\GroupQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Group extends GroupQueryHandler implements MessageSubscriberInterface
{

}