<?php

namespace Apto\Catalog\Application\Backend\Commands\Filter;

class UpdateFilterCategory extends AbstractAddFilterCategory
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
     * @param int $position
     */
    public function __construct(string $id, array $name, string $identifier, int $position = 0)
    {
        parent::__construct($name, $identifier, $position);
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