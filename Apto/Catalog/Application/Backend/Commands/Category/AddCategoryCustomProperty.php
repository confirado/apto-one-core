<?php

namespace Apto\Catalog\Application\Backend\Commands\Category;

use Apto\Base\Application\Core\CommandInterface;

class AddCategoryCustomProperty implements CommandInterface
{
    /**
     * @var string
     */
    private $categoryId;

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
     * AddCategoryCustomProperty constructor.
     * @param string $categoryId
     * @param string $key
     * @param string|array $value
     * @param bool $translatable
     */
    public function __construct(string $categoryId,string $key, $value, bool $translatable = false)
    {
        $this->categoryId = $categoryId;
        $this->key = $key;
        $this->value = $translatable ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
        $this->translatable = $translatable;
    }

    /**
     * @return string
     */
    public function getCategoryId(): string
    {
        return $this->categoryId;
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
