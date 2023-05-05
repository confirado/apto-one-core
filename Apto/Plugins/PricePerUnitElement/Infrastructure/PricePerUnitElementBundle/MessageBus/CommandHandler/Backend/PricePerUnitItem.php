<?php

namespace Apto\Plugins\PricePerUnitElement\Infrastructure\PricePerUnitElementBundle\MessageBus\CommandHandler\Backend;

use Apto\Plugins\PricePerUnitElement\Application\Backend\Commands\PricePerUnitItem\PricePerUnitItemCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class PricePerUnitItem extends PricePerUnitItemCommandHandler implements MessageSubscriberInterface
{

}