<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Property;

use Apto\Base\Application\Core\CommandInterface;

class RemovePropertyCustomProperty implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $key;

    /**
     * RemovePropertyCustomProperty constructor.
     * @param string $id
     * @param string $key
     */
    public function __construct(string $id, string $key)
    {
        $this->id = $id;
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}