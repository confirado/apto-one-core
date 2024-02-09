<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Frontend;

use Apto\Base\Application\Frontend\Commands\FrontendUser\ResetPasswordHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ResetPassword extends ResetPasswordHandler implements MessageSubscriberInterface
{

}
