<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Condition;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\Product;

class Condition extends Criterion
{
    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Identifier
     */
    protected $identifier;

    /**
     * @param AptoUuid $id
     * @param Product $product
     * @param Identifier $identifier
     * @param CriterionOperator $operator
     * @param int|null $type
     * @param AptoUuid|null $sectionId
     * @param AptoUuid|null $elementId
     * @param string|null $property
     * @param ComputedProductValue|null $computedProductValue
     * @param string|null $value
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidTypeException
     * @throws CriterionInvalidValueException
     */
    final public function __construct(
        Product               $product,
        AptoUuid              $id,
        Identifier            $identifier,
        CriterionOperator     $operator,
        ?int                  $type,
        ?AptoUuid             $sectionId,
        ?AptoUuid             $elementId,
        string                $property = null,
        ?ComputedProductValue $computedProductValue = null,
        ?string               $value = null
    )
    {
        parent::__construct(
            $product,
            $id,
            $operator,
            $type,
            $sectionId,
            $elementId,
            $property,
            $computedProductValue,
            $value
        );

        if (null !== $elementId && null !== $property && !array_key_exists($property, $product->getElementSelectableValues($sectionId, $elementId))) {
            throw new CriterionInvalidPropertyException('The given property \'' . $property . '\' is not defined in the given element\'s definition.');
        }
        $this->identifier = $identifier;
    }

    /**
     * @return Identifier
     */
    public function getIdentifier(): Identifier
    {
        return $this->identifier;
    }

    /**
     * @param Identifier $identifier
     * @return $this
     */
    public function setIdentifier(Identifier $identifier): Condition
    {
        $this->identifier = $identifier;
        return $this;
    }
}
