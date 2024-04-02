<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Condition;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Backend\Commands\Product\ProductChildHandler;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidOperatorException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidPropertyException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidTypeException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidValueException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionOperator;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;

class ProductConditionHandler extends ProductChildHandler
{
    /**
     * @param AddProductCondition $command
     * @return void
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidTypeException
     * @throws CriterionInvalidValueException
     * @throws InvalidUuidException
     * @throws \Exception
     */
    public function handleAddProductCondition(AddProductCondition $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        $computedProductValue = null;
        if ($command->getComputedValueId() !== null) {
            /** @var ComputedProductValue $value */
            foreach ($product->getComputedProductValues() as $value) {
                if ($value->getId()->getId() === $command->getComputedValueId()) {
                    $computedProductValue = $value;
                }
            }
        }

        if (null !== $product) {
            $product->addProductCondition(
                new Identifier ($command->getIdentifier()),
                new CriterionOperator ($command->getOperator()),
                $command->getType(),
                null !== $command->getSectionId() ? new AptoUuid($command->getSectionId()) : null,
                null !== $command->getElementId() ? new AptoUuid($command->getElementId()) : null,
                $command->getProperty(),
                $computedProductValue,
                $command->getValue()
            );
            $this->productRepository->update($product);
        }
    }

    /**
     * @param UpdateProductCondition $command
     * @return void
     * @throws CriterionInvalidOperatorException
     * @throws InvalidUuidException
     */
    public function handleUpdateProductCondition(UpdateProductCondition $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $conditionId = new AptoUuid($command->getConditionId());
            $identifier = new Identifier($command->getIdentifier());
            $type = $command->getType();
            $operator = new CriterionOperator($command->getOperator());
            $value = $command->getValue();
            $computedValueId = new AptoUuid($command->getComputedValueId());
            $sectionId = new AptoUuid($command->getSectionId());
            $elementId = new AptoUuid($command->getElementId());
            $property = $command->getProperty();

            $product->setProductCondition($conditionId, $identifier, $type, $computedValueId, $sectionId, $elementId, $property, $operator, $value);

            $this->productRepository->update($product);
        }
    }

    /**
     * @param RemoveProductCondition $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleRemoveProductCondition(RemoveProductCondition $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $product->removeCondition(
                new AptoUuid($command->getConditionId())
            );

            $this->productRepository->update($product);
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddProductCondition::class => [
            'method' => 'handleAddProductCondition',
            'bus' => 'command_bus'
        ];

        yield UpdateProductCondition::class => [
            'method' => 'handleUpdateProductCondition',
            'bus' => 'command_bus'
        ];

        yield RemoveProductCondition::class => [
            'method' => 'handleRemoveProductCondition',
            'bus' => 'command_bus'
        ];
    }
}
