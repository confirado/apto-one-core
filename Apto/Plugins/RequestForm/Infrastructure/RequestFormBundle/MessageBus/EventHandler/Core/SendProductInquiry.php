<?php

namespace Apto\Plugins\RequestForm\Infrastructure\RequestFormBundle\MessageBus\EventHandler\Core;

use Apto\Plugins\RequestForm\Application\Core\Subscribers\SendProductInquiryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class SendProductInquiry extends SendProductInquiryHandler implements MessageSubscriberInterface
{

}