<?php

namespace Apto\Catalog\Application\Frontend\Commands\Configuration;

class AddImmutableConfiguration extends ConfigurationCommand
{
    /**
     * @var string
     */
    private $id;

    /**
     * AddImmutableConfiguration constructor.
     * @param string $productId
     * @param array $state
     * @param string $id
     */
    public function __construct(string $productId, array $state, $id = '')
    {
        parent::__construct($productId, $state);
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
