<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Section;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class UpdateProductSection extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var ?string
     */
    private $sectionIdentifier;

    /**
     * @var array
     */
    private $sectionName;

    /**
     * @var array
     */
    private $sectionDescription;

    /**
     * @var bool
     */
    private $allowMultiple;

    /**
     * @var array
     */
    private $repeatable;

    /**
     * @var null|string
     */
    private $previewImage;

    /**
     * @var bool
     */
    private $isZoomable;

    /**
     * @var string|null
     */
    private $groupId;

    /**
     * @var bool
     */
    private $isHidden;

    /**
     * @var int
     */
    private $position;

    /**
     * UpdateProductSection constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string|null $sectionIdentifier
     * @param array $sectionName
     * @param array $sectionDescription
     * @param $previewImage
     * @param bool $allowMultiple
     * @param array $repeatable
     * @param string|null $groupId
     * @param bool $isHidden
     * @param int $position
     * @param bool|null $isZoomable
     */
    public function __construct(
        string $productId,
        string $sectionId,
        ?string $sectionIdentifier,
        array $sectionName,
        array $sectionDescription,
        $previewImage,
        bool $allowMultiple,
        array $repeatable,
        string $groupId = null,
        bool $isHidden = false,
        int $position = 0,
        bool $isZoomable = null
    ) {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->sectionIdentifier = $sectionIdentifier;
        $this->sectionName = $sectionName;
        $this->sectionDescription = $sectionDescription;
        $this->allowMultiple = $allowMultiple;
        $this->repeatable = $repeatable;
        $this->groupId = $groupId;
        $this->isHidden = $isHidden;
        $this->position = $position;
        $this->isZoomable = $isZoomable;
        $this->previewImage = $previewImage;
    }

    /**
     * @return string
     */
    public function getSectionId(): string
    {
        return $this->sectionId;
    }

    /**
     * @return string|null
     */
    public function getSectionIdentifier(): ?string
    {
        return $this->sectionIdentifier;
    }

    /**
     * @return array
     */
    public function getSectionName(): array
    {
        return $this->sectionName;
    }

    /**
     * @return array
     */
    public function getSectionDescription(): array
    {
        return $this->sectionDescription;
    }

    /**
     * @return bool
     */
    public function getAllowMultiple(): bool
    {
        return $this->allowMultiple;
    }

    /**
     * @return array
     */
    public function getRepeatable(): array
    {
        return $this->repeatable;
    }

    /**
     * @return string|null
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @return bool
     */
    public function getIsHidden(): bool
    {
        return $this->isHidden;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return string|null
     */
    public function getPreviewImage(): ?string
    {
        return $this->previewImage;
    }

    /**
     * @return bool
     */
    public function getIsZoomable(): ?bool
    {
        return $this->isZoomable;
    }
}
