<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Backend;

use Apto\Base\Application\Backend\Query\MediaFile\ListMediaFilesHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class MediaFile extends ListMediaFilesHandler implements MessageSubscriberInterface
{

}