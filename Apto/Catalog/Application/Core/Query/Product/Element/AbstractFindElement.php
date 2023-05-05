<?php

namespace Apto\Catalog\Application\Core\Query\Product\Element;

use Apto\Base\Application\Core\PublicQueryInterface;

abstract class AbstractFindElement implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $elementId;

    /**
     * FindElement constructor.
     * @param string $elementId
     */
    public function __construct(string $elementId)
    {
        $this->elementId = $elementId;
    }

    /**
     * @return string
     */
    public function getElementId(): string
    {
        return $this->elementId;
    }
}