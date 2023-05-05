<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Commands\Material;

use Apto\Base\Application\Core\PublicCommandInterface;

class IncrementMaterialClicks implements PublicCommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
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