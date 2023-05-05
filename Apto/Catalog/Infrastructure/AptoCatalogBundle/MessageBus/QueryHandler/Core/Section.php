<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\Product\Section\SectionQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Section extends SectionQueryHandler implements MessageSubscriberInterface
{

}