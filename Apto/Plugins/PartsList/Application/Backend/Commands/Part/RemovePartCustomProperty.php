<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

class RemovePartCustomProperty extends PartChildCommand
{
    /**
     * @var string
     */
    private $key;

    /**
     * @param string $partId
     * @param string $key
     */
    public function construct(string $partId, string $key)
    {
        parent::__construct($partId);
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
