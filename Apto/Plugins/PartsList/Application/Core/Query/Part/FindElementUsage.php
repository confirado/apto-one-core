<?php

namespace Apto\Plugins\PartsList\Application\Core\Query\Part;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindElementUsage implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindElementUsage constructor.
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