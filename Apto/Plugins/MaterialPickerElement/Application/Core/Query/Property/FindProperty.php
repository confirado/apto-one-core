<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Property;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindProperty implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindProperty constructor.
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