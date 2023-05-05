<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Backend;

use Apto\Base\Application\Backend\Commands\MediaFile\RemoveMediaFileHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class RemoveMediaFile extends RemoveMediaFileHandler implements MessageSubscriberInterface
{

}