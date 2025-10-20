<?php

namespace Apto\Plugins\PdfGenerator\Infrastructure\PdfGeneratorBundle\MessageBus\EventHandler\Core;

use Apto\Plugins\PdfGenerator\Application\Core\Subscribers\SendProductInquiryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class SendProductInquiry extends SendProductInquiryHandler implements MessageSubscriberInterface
{

}
