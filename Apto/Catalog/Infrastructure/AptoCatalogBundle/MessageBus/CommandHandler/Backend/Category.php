<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\Category\CategoryCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Category extends CategoryCommandHandler implements MessageSubscriberInterface
{

}