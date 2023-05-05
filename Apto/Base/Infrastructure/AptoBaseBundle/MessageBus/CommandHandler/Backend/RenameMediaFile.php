<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Backend;

use Apto\Base\Application\Backend\Commands\MediaFile\RenameMediaFileHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class RenameMediaFile extends RenameMediaFileHandler implements MessageSubscriberInterface
{

}