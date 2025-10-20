<?php

namespace Apto\Plugins\PdfGenerator\Infrastructure\PdfGeneratorBundle\MessageBus\EventHandler\Core;

use Apto\Plugins\PdfGenerator\Application\Core\Subscribers\CreateProductPdfInquiryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class CreateProductPdfInquiry extends CreateProductPdfInquiryHandler implements MessageSubscriberInterface
{

}
