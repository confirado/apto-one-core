<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Property;

use Apto\Base\Application\Core\CommandInterface;

class AddPropertyCustomProperty implements CommandInterface
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
     * @var string
     */
    private $value;

    /**
     * AddPropertyCustomProperty constructor.
     * @param string $id
     * @param string $key
     * @param string $value
     */
    public function __construct(string $id, string $key, string $value)
    {
        $this->id = $id;
        $this->key = $key;
        $this->value = $value;
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

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}