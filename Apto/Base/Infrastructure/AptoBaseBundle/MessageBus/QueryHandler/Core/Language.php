<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Core;

use Apto\Base\Application\Core\Query\Language\LanguageQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Language extends LanguageQueryHandler implements MessageSubscriberInterface
{

}