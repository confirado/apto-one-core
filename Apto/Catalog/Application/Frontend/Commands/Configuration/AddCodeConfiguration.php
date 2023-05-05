<?php

namespace Apto\Catalog\Application\Frontend\Commands\Configuration;

class AddCodeConfiguration extends ConfigurationCommand
{
    /**
     * @var string|null
     */
    private $id;

    /**
     * AddCodeConfiguration constructor.
     * @param string $productId
     * @param array $state
     * @param string|null $id
     */
    public function __construct(string $productId, array $state, string $id = null)
    {
        $this->id = $id;
        parent::__construct($productId, $state);
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }
}