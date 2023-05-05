<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Core;

use Apto\Base\Application\Backend\Query\FrontendUser\FrontendUserQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class FrontendUser extends FrontendUserQueryHandler implements MessageSubscriberInterface
{

}
