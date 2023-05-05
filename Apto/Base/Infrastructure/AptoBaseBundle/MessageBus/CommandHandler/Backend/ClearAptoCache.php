<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Backend;

use Apto\Base\Application\Backend\Commands\Cache\ClearAptoCacheHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ClearAptoCache extends ClearAptoCacheHandler implements MessageSubscriberInterface
{

}