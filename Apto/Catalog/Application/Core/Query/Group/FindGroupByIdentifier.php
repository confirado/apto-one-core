<?php

namespace Apto\Catalog\Application\Core\Query\Group;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindGroupByIdentifier implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * FindGroup constructor.
     * @param string $identifier
     */
    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}