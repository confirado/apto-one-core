<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\MessageBus\QueryHandler\Core;

use Apto\Catalog\Application\Core\Query\Product\Element\RenderImageQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class RenderImage extends RenderImageQueryHandler implements MessageSubscriberInterface
{

}