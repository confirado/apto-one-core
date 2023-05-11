<?php

namespace Apto\Plugins\ImageUpload\Infrastructure\ImageUploadBundle\MessageBus\CommandHandler\Core;

use Apto\Plugins\ImageUpload\Application\Core\Commands\UploadUserImageFileHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class UploadUserImage extends UploadUserImageFileHandler implements MessageSubscriberInterface
{

}
