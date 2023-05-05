<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Property;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindGroupProperties implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $searchString;

    /**
     * FindGroupProperties constructor.
     * @param string $id
     * @param string $searchString
     */
    public function __construct(string $id, string $searchString = '')
    {
        $this->id = $id;
        $this->searchString = $searchString;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSearchString(): string
    {
        return $this->searchString;
    }
}