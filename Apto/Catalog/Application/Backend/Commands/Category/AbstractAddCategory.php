<?php

namespace Apto\Catalog\Application\Backend\Commands\Category;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddCategory implements CommandInterface
{
    /**
     * @var array
     */
    private $name;

    /**
     * @var array
     */
    private $description;

    /**
     * @var string
     */
    protected $parent;

    /**
     * @var null|string
     */
    private $previewImage;

    /**
     * @var int
     */
    private $position;

    /**
     * AddCategory constructor.
     * @param array $name
     * @param array $description
     * @param int $position
     * @param string|null $parent
     * @param null $previewImage
     */
    public function __construct(array $name, array $description, int $position = 0, string $parent = null, $previewImage = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->parent = $parent;
        $this->previewImage = $previewImage;
        $this->position = $position;
    }

    /**
     * @return array
     */
    public function getName(): array
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getDescription(): array
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * @return null|string
     */
    public function getPreviewImage()
    {
        return $this->previewImage;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }
}
