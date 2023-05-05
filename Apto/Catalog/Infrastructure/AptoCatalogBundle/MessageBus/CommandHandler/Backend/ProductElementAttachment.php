<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\CommandHandler\Backend;

use Apto\Catalog\Application\Backend\Commands\Product\Element\ProductElementAttachmentHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ProductElementAttachment extends ProductElementAttachmentHandler implements MessageSubscriberInterface
{

}
