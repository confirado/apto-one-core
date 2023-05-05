<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Core;

use Apto\Base\Application\Core\Query\ContentSnippet\ContentSnippetQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ContentSnippet extends ContentSnippetQueryHandler implements MessageSubscriberInterface
{

}