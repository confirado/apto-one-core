<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\Product\ProductQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Product extends ProductQueryHandler implements MessageSubscriberInterface
{

}