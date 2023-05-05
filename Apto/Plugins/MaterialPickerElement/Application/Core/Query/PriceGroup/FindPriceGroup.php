<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\PriceGroup;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPriceGroup implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindPriceGroup constructor.
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