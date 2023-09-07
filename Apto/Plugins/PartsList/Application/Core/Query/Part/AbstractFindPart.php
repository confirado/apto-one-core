<?php

namespace Apto\Plugins\PartsList\Application\Core\Query\Part;

use Apto\Base\Application\Core\PublicQueryInterface;

abstract class AbstractFindPart implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindCustomer constructor.
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