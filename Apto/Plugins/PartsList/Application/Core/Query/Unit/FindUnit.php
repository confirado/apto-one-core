<?php

namespace Apto\Plugins\PartsList\Application\Core\Query\Unit;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindUnit implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindUnit constructor.
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