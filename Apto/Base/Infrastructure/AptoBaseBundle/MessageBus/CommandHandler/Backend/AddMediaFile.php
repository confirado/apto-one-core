<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Backend;

use Apto\Base\Application\Backend\Commands\MediaFile\AddMediaFileHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class AddMediaFile extends AddMediaFileHandler implements MessageSubscriberInterface
{

}