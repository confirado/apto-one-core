<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Frontend;

use Apto\Catalog\Application\Frontend\Query\Product\ProductQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ConfigurableProduct extends ProductQueryHandler implements MessageSubscriberInterface
{

}