<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Backend;

use Apto\Catalog\Application\Backend\Query\Price\PriceQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Price extends PriceQueryHandler implements MessageSubscriberInterface
{

}