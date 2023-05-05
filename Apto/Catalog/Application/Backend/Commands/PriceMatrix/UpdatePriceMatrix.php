<?php

namespace Apto\Catalog\Application\Backend\Commands\PriceMatrix;

class UpdatePriceMatrix extends AbstractAddPriceMatrix
{
    /**
     * @var string
     */
    private $id;

    /**
     * UpdatePriceMatrix constructor.
     * @param string $id
     * @param array $name
     */
    public function __construct(string $id, array $name)
    {
        parent::__construct($name);
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