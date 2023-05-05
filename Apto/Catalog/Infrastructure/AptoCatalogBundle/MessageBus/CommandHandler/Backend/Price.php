<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\Price\PriceCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Price extends PriceCommandHandler implements MessageSubscriberInterface
{

}