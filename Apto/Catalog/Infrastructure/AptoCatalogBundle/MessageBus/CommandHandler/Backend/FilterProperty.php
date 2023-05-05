<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\Filter\FilterPropertyCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class FilterProperty extends FilterPropertyCommandHandler implements MessageSubscriberInterface
{

}