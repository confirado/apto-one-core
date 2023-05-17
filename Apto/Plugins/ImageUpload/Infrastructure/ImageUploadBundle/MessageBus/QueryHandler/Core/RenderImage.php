<?php

namespace Apto\Plugins\ImageUpload\Infrastructure\ImageUploadBundle\MessageBus\QueryHandler\Core;

use Apto\Plugins\ImageUpload\Application\Core\Query\RenderImage\RenderImageQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class RenderImage extends RenderImageQueryHandler implements MessageSubscriberInterface
{

}
