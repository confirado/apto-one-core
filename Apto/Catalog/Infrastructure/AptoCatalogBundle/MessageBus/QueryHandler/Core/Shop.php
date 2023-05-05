<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\Shop\ShopQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Shop extends ShopQueryHandler implements MessageSubscriberInterface
{

}