<?php

namespace Apto\Base\Application\Backend\Commands\Language;

use Apto\Base\Application\Core\CommandInterface;

class RemoveLanguage implements CommandInterface
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