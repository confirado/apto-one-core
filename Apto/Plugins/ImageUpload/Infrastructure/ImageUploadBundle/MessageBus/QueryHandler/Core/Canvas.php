<?php

namespace Apto\Plugins\ImageUpload\Infrastructure\ImageUploadBundle\MessageBus\QueryHandler\Core;

use Apto\Plugins\ImageUpload\Application\Core\Query\CanvasQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Canvas extends CanvasQueryHandler implements MessageSubscriberInterface
{

}
