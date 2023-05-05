<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Core;

use Apto\Base\Application\Core\Query\MediaFile\MediaFileQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class MediaFile extends MediaFileQueryHandler implements MessageSubscriberInterface
{

}