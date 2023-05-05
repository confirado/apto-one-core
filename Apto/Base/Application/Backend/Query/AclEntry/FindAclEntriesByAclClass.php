<?php

namespace Apto\Base\Application\Backend\Query\AclEntry;

use Apto\Base\Application\Core\QueryInterface;

class FindAclEntriesByAclClass implements QueryInterface
{
    /**
     * @var string
     */
    private $aclClass;

    /**
     * FindAclEntries constructor.
     * @param string $aclClass
     */
    public function __construct(string $aclClass)
    {
        $this->aclClass = $aclClass;
    }

    /**
     * @return string
     */
    public function getAclClass(): string
    {
        return $this->aclClass;
    }
}