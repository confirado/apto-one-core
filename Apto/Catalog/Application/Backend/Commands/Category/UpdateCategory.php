<?php

namespace Apto\Catalog\Application\Backend\Commands\Category;

class UpdateCategory extends AbstractAddCategory
{
    /**
     * @var string
     */
    private $id;

    /**
     * UpdateCategory constructor.
     * @param string $id
     * @param array $name
     * @param array $description
     * @param int $position
     * @param string|null $parent
     * @param string|null $previewImage
     */
    public function __construct(string $id, array $name, array $description, int $position = 0, string $parent = null, string $previewImage = null)
    {
        parent::__construct($name, $description, $position, $parent, $previewImage);
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