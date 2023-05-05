<?php

namespace Apto\Base\Application\Core\Query\Language;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindLanguage implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindLanguage constructor.
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