<?php

namespace Apto\Catalog\Application\Backend\Commands\Category;

use Apto\Base\Application\Core\CommandInterface;

abstract class CategoryChildCommand implements CommandInterface
{
    /**
     * @var string
     */
    private $categoryId;

    /**
     * CategorySectionCommand constructor.
     * @param string $categoryId
     */
    public function __construct(string $categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @return string
     */
    public function getCategoryId(): string
    {
        return $this->categoryId;
    }
}