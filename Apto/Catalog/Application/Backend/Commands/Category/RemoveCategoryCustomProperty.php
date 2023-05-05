<?php

namespace Apto\Catalog\Application\Backend\Commands\Category;

class RemoveCategoryCustomProperty extends CategoryChildCommand
{
    /**
     * @var string
     */
    private $key;

    /**
     * RemoveCategoryPrice constructor.
     * @param string $categoryId
     * @param string $key
     */
    public function __construct(string $categoryId,  string $key)
    {
        parent::__construct($categoryId);
        $this->key = $key;
    }
    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}