<?php

namespace Apto\Catalog\Application\Backend\Commands\Product;

use Apto\Base\Application\Core\CommandInterface;

class CopyProductSection implements CommandInterface
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
     * CopyProductSection constructor.
     * @param string $id
     * @param string $sectionId
     */
    public function __construct(string $id, string $sectionId)
    {
        $this->id = $id;
        $this->sectionId = $sectionId;
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
}