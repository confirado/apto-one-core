<?php

namespace Apto\Plugins\ImageUpload\Infrastructure\ImageUploadBundle\MessageBus\CommandHandler\Backend;

use Apto\Plugins\ImageUpload\Application\Backend\Commands\CanvasCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Canvas extends CanvasCommandHandler implements MessageSubscriberInterface
{

}
