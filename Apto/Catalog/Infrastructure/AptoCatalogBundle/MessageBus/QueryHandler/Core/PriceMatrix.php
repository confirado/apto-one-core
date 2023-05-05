<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\PriceMatrix\PriceMatrixQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class PriceMatrix extends PriceMatrixQueryHandler implements MessageSubscriberInterface
{

}