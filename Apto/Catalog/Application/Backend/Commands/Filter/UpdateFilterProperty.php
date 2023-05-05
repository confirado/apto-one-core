<?php

namespace Apto\Catalog\Application\Backend\Commands\Filter;

class UpdateFilterProperty extends AbstractAddFilterProperty
{
    /**
     * @var string
     */
    private $id;

    /**
     * UpdateFilterProperty constructor.
     * @param string $id
     * @param array $name
     * @param string $identifier
     * @param array $filterCategoryIds
     */
    public function __construct(string $id, array $name, string $identifier, array $filterCategoryIds)
    {
        parent::__construct($name, $identifier, $filterCategoryIds);
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