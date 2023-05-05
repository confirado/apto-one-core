<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Property;

use Apto\Base\Application\Core\CommandInterface;

class UpdateProperty implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $name;

    /**
     * UpdateProperty constructor.
     * @param string $id
     * @param array $name
     */
    public function __construct(string $id, array $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getName(): array
    {
        return $this->name;
    }
}