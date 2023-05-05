<?php
namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Doctrine\Common\Collections\Collection;

interface ElementDefinitionCopyAware
{
    /**
     * @param Collection $entityMapping
     * @return ElementDefinition
     */
    public function copy(Collection &$entityMapping): ElementDefinition;
}