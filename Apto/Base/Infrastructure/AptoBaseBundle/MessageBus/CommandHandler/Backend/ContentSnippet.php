<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Backend;

use Apto\Base\Application\Backend\Commands\ContentSnippet\ContentSnippetCommandHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ContentSnippet extends ContentSnippetCommandHandler implements MessageSubscriberInterface
{

}