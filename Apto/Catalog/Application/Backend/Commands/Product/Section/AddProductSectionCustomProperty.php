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
     * AddProductSectionCustomProperty constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $key
     * @param string $value
     * @param bool $translatable
     */
    public function __construct(string $productId, string $sectionId, string $key, $value, bool $translatable = false)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->key = $key;
        $this->value = $translatable ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
        $this->translatable = $translatable;
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
}
