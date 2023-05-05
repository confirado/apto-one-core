<?php

namespace Apto\Plugins\SelectBoxElement\Application\Core\Query\SelectBoxItem;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindSelectBoxItems implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $elementId;

    /**
     * FindSelectBoxItems constructor.
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