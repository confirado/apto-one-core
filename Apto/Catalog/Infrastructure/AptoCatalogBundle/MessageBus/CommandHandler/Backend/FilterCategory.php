<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\Filter\FilterCategoryCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class FilterCategory extends FilterCategoryCommandHandler implements MessageSubscriberInterface
{

}