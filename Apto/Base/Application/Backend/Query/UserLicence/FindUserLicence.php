<?php

namespace Apto\Base\Application\Backend\Query\UserLicence;

use Apto\Base\Application\Core\QueryInterface;

class FindUserLicence implements QueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindUserLicence constructor.
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