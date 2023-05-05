<?php

namespace Apto\Plugins\SelectBoxElement\Application\Core\Query\SelectBoxItem;

use Apto\Base\Application\Core\PublicQueryInterface;

abstract class AbstractFindSelectBoxItem implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindSelectBoxItem constructor.
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