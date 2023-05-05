<?php

namespace Apto\Catalog\Application\Backend\Commands\Filter;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddFilterProperty implements CommandInterface
{
    /**
     * @var array
     */
    private $name;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var array
     */
    private $filterCategoryIds;

    /**
     * AddFilterProperty constructor.
     * @param array $name
     * @param string $identifier
     * @param array $filterCategoryIds
     */
    public function __construct(array $name, string $identifier, array $filterCategoryIds)
    {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->filterCategoryIds = $filterCategoryIds;
    }

    /**
     * @return array
     */
    public function getName(): array
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return array
     */
    public function getFilterCategoryIds(): array
    {
        return $this->filterCategoryIds;
    }
}
