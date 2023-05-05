<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Backend;

use Apto\Base\Application\Backend\Query\UserLicence\UserLicenceQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class UserLicence extends UserLicenceQueryHandler implements MessageSubscriberInterface
{

}