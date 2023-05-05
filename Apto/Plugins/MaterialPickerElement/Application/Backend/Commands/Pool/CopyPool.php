<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Pool;

use Apto\Base\Application\Core\CommandInterface;

class CopyPool implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * CopyPool constructor.
     * @param string $id
     */
    public function __construct(string $id) {
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
