<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Backend;

use Apto\Base\Application\Backend\Commands\Settings\SettingsCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Settings extends SettingsCommandHandler implements MessageSubscriberInterface
{

}
