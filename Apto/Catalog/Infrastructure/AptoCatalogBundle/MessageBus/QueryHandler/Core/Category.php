<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\Category\CategoryQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Category extends CategoryQueryHandler implements MessageSubscriberInterface
{

}