<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\Filter\FilterCategoryQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class FilterCategory extends FilterCategoryQueryHandler implements MessageSubscriberInterface
{

}