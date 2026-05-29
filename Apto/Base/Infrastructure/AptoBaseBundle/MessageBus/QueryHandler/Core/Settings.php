<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Core;

use Apto\Base\Application\Core\Query\Settings\SettingsQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Settings extends SettingsQueryHandler implements MessageSubscriberInterface
{

}
