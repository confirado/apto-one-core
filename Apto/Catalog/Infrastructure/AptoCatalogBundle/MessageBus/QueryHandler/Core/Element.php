<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\Product\Element\ElementQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Element extends ElementQueryHandler implements MessageSubscriberInterface
{

}