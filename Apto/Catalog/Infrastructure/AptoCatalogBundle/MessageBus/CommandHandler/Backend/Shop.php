<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\Shop\ShopCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Shop extends ShopCommandHandler implements MessageSubscriberInterface
{

}