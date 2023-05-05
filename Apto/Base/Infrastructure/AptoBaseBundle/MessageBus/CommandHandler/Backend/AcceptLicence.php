<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Backend;

use Apto\Base\Application\Backend\Commands\User\AcceptLicenceHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class AcceptLicence extends AcceptLicenceHandler implements MessageSubscriberInterface
{

}