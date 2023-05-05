<?php
namespace Apto\Catalog\Domain\Core\Model\Product\Element;

interface ElementDefinitionDefaultValues
{
    /**
     * Returns all default values in key => value format
     * @return array
     */
    public function getDefaultValues(): array;
}