<?php

namespace Apto\Plugins\FloatInputElement\Application\Core\Query\FloatInputItem;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindFloatInputPrices implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindFloatInputPrices constructor.
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