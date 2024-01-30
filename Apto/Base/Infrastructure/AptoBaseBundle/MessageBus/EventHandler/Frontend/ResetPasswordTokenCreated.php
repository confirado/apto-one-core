<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\EventHandler\Frontend;

use Apto\Base\Domain\Frontend\Subscribers\ResetPasswordTokenCreatedHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ResetPasswordTokenCreated extends ResetPasswordTokenCreatedHandler implements MessageSubscriberInterface
{

}
