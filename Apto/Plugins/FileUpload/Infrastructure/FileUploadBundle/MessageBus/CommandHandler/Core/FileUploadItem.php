<?php

namespace Apto\Plugins\FileUpload\Infrastructure\FileUploadBundle\MessageBus\CommandHandler\Core;

use Apto\Plugins\FileUpload\Application\Core\Commands\FileUpload\UploadFileHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class FileUploadItem extends UploadFileHandler implements MessageSubscriberInterface
{

}