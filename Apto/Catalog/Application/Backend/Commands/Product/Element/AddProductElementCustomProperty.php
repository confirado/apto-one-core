<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class AddProductElementCustomProperty extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var string
     */
    private $elementId;

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
     * AddProductElementCustomProperty constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param string $key
     * @param array|string $value
     * @param bool $translatable
     */
    public function __construct(string $productId, string $sectionId, string $elementId, string $key, $value, bool $translatable = false)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
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
    public function getElementId(): string
    {
        return $this->elementId;
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
