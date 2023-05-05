<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Backend;

use Apto\Base\Application\Backend\Commands\MediaFile\UploadMediaFileHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class UploadMediaFile extends UploadMediaFileHandler implements MessageSubscriberInterface
{

}