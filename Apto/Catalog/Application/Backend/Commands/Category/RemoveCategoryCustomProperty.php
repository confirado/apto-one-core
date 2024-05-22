<?php

namespace Apto\Catalog\Application\Backend\Commands\Category;

class RemoveCategoryCustomProperty extends CategoryChildCommand
{
    /**
     * @var string
     */
    private $id;

    /**
     * RemoveCategoryPrice constructor.
     * @param string $categoryId
     * @param string $key
     */
    public function __construct(string $categoryId,  string $id)
    {
        parent::__construct($categoryId);
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
