<?php

namespace Apto\Base\Application\Backend\Commands\Language;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddLanguage implements CommandInterface
{
    /**
     * @var array
     */
    private $name;

    /**
     * @var string
     */
    private $isocode;

    /**
     * AddShop constructor.
     * @param array $name
     * @param string $isocode
     */
    public function __construct(array $name, string $isocode)
    {
        $this->name = $name;
        $this->isocode = $isocode;
    }

    /**
     * @return array
     */
    public function getName(): array
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIsocode(): string
    {
        return $this->isocode;
    }
}