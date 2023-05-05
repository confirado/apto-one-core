<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Backend;

use Apto\Base\Application\Backend\Commands\Language\LanguageCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Language extends LanguageCommandHandler implements MessageSubscriberInterface
{

}