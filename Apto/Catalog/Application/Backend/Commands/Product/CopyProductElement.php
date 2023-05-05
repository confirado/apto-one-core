<?php

namespace Apto\Catalog\Application\Backend\Commands\Product;

use Apto\Base\Application\Core\CommandInterface;

class CopyProductElement implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var string
     */
    private $elementId;

    /**
     * CopyProductElement constructor.
     * @param string $id
     * @param string $sectionId
     * @param string $elementId
     */
    public function __construct(string $id, string $sectionId, string $elementId)
    {
        $this->id = $id;
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
}