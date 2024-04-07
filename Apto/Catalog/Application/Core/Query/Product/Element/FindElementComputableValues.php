<?php

namespace Apto\Catalog\Application\Core\Query\Product\Element;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindElementComputableValues implements PublicQueryInterface
{
    /**
     * @var array
     */
    private $state;

    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var string
     */
    private $elementId;

    /**
     * @var int
     */
    private int $repetition;

    /**
     * @param array  $state
     * @param string $sectionId
     * @param string $elementId
     * @param int    $repetition
     */
    public function __construct(array $state, string $sectionId, string $elementId, int $repetition)
    {
        $this->state = $state;
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->repetition = $repetition;
    }

    /**
     * @return array
     */
    public function getState(): array
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getSectionId(): string
    {
        return $this->sectionId;
    }

    /**
     * @return string
     */
    public function getElementId(): string
    {
        return $this->elementId;
    }

    /**
     * @return int
     */
    public function getRepetition(): int
    {
        return $this->repetition;
    }
}
