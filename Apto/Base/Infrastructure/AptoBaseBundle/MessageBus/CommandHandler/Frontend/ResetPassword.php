<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Frontend;

use Apto\Base\Domain\Frontend\Commands\ResetPasswordHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ResetPassword extends ResetPasswordHandler implements MessageSubscriberInterface
{

}
