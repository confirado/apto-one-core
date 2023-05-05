<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Property;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddGroup implements CommandInterface
{
    /**
     * @var array
     */
    private $name;

    /**
     * @var bool
     */
    protected $allowMultiple;

    /**
     * AddGroup constructor.
     * @param array $name
     * @param bool $allowMultiple
     */
    public function __construct(array $name, bool $allowMultiple) {
        $this->name = $name;
        $this->allowMultiple = $allowMultiple;
    }

    /**
     * @return array
     */
    public function getName(): array
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function getAllowMultiple(): bool
    {
        return $this->allowMultiple;
    }
}