<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Material;

use Apto\Base\Application\Core\PublicQueryInterface;

abstract class AbstractFindMaterial implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindMaterial constructor.
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