<?php

namespace Apto\Plugins\ImageUpload\Infrastructure\ImageUploadBundle\MessageBus\QueryHandler\Core;

use Apto\Plugins\ImageUpload\Application\Core\Query\ImageMetaDataQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ImageMetaData extends ImageMetaDataQueryHandler implements MessageSubscriberInterface
{

}
