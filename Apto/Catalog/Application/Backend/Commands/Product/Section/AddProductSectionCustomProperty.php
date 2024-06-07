<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Section;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class AddProductSectionCustomProperty extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @var bool
     */
    private $translatable;

    /**
     * @var string|null
     */
    private $productConditionId;

    /**
     * @param string $productId
     * @param string $sectionId
     * @param string $key
     * @param $value
     * @param bool $translatable
     * @param string|null $productConditionId
     */
    public function __construct(string $productId, string $sectionId, string $key, $value, bool $translatable = false, ?string $productConditionId = null)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->key = $key;
        $this->value = $translatable ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
        $this->translatable = $translatable;
        $this->productConditionId = $productConditionId;
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
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function getTranslatable(): bool
    {
        return $this->translatable;
    }

    /**
     * @return string|null
     */
    public function getProductConditionId(): ?string
    {
        return $this->productConditionId;
    }
}
