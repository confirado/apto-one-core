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
use Apto\Catalog\Domain\Core\Model\Product\IdentifierUniqueException;

class ProductConditionHandler extends ProductChildHandler
{
    /**
     * @param AddConditionSet $command
     * @return void
     * @throws IdentifierUniqueException
     */
    public function handleAddConditionSet(AddConditionSet $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            return;
        }

        $product->addConditionSet(
            new Identifier($command->getIdentifier())
        );
        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param UpdateConditionSet $command
     * @return void
     * @throws CriterionInvalidOperatorException
     * @throws IdentifierUniqueException
     * @throws InvalidUuidException
     */
    public function handleUpdateConditionSet(UpdateConditionSet $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            return;
        }

        $conditionSetId = new AptoUuid($command->getConditionSetId());
        $product
            ->setConditionSetIdentifier(
                $conditionSetId,
                new Identifier($command->getIdentifier())
            )
            ->setConditionSetConditionsOperator(
                $conditionSetId,
                $command->getConditionsOperator()
            )
        ;

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param RemoveConditionSet $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleRemoveConditionSet(RemoveConditionSet $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            return;
        }

        $product->removeConditionSet(
            new AptoUuid($command->getConditionSetId())
        );
        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param AddConditionSetCondition $command
     * @return void
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidTypeException
     * @throws CriterionInvalidValueException
     * @throws InvalidUuidException
     */
    public function handleAddConditionSetCondition(AddConditionSetCondition $command)
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
            $product->addConditionSetCondition(
                new AptoUuid($command->getConditionSetId()),
                new CriterionOperator($command->getOperator()),
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
     * @param UpdateConditionSetCondition $command
     * @return void
     * @throws CriterionInvalidOperatorException
     * @throws InvalidUuidException
     */
    public function handleUpdateConditionSetCondition(UpdateConditionSetCondition $command): void
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $conditionSetId = new AptoUuid($command->getConditionSetId());
            $conditionId = new AptoUuid($command->getConditionId());
            $type = $command->getType();
            $operator = new CriterionOperator($command->getOperator());
            $value = $command->getValue();
            $computedValueId = $command->getComputedValueId() ? new AptoUuid($command->getComputedValueId()) : null;
            $sectionId = $command->getSectionId() ? new AptoUuid($command->getSectionId()) : null;
            $elementId = $command->getElementId() ? new AptoUuid($command->getElementId()) : null;
            $property = $command->getProperty();

            $product->setConditionSetCondition($conditionSetId, $conditionId, $type, $operator, $value, $computedValueId, $sectionId, $elementId, $property);

            $this->productRepository->update($product);
        }
    }

    /**
     * @param CopyConditionSetCondition $command
     * @return void
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidTypeException
     * @throws CriterionInvalidValueException
     * @throws InvalidUuidException
     */
    public function handleCopyConditionSetCondition(CopyConditionSetCondition $command): void
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $product->copyConditionSetCondition(
                new AptoUuid($command->getConditionSetId()),
                new AptoUuid($command->getConditionId())
            );
            $this->productRepository->update($product);
        }
    }

    /**
     * @param RemoveConditionSetCondition $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleRemoveConditionSetCondition(RemoveConditionSetCondition $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $product->removeConditionSetCondition(
                new AptoUuid($command->getConditionSetId()),
                new AptoUuid($command->getConditionId())
            );

            $this->productRepository->update($product);
        }
    }

    /**
     * @param AddCondition $command
     * @return void
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidTypeException
     * @throws CriterionInvalidValueException
     * @throws InvalidUuidException
     * @throws \Exception
     */
    public function handleAddProductCondition(AddCondition $command)
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
     * @param UpdateCondition $command
     * @return void
     * @throws CriterionInvalidOperatorException
     * @throws InvalidUuidException
     */
    public function handleUpdateProductCondition(UpdateCondition $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $conditionId = new AptoUuid($command->getConditionId());
            $identifier = new Identifier($command->getIdentifier());
            $type = $command->getType();
            $operator = new CriterionOperator($command->getOperator());
            $value = $command->getValue();
            $computedValueId = $command->getComputedValueId() ? new AptoUuid($command->getComputedValueId()) : null;
            $sectionId = $command->getSectionId() ? new AptoUuid($command->getSectionId()) : null;
            $elementId = $command->getElementId() ? new AptoUuid($command->getElementId()) : null;
            $property = $command->getProperty();

            $product->setProductCondition($conditionId, $identifier, $type, $computedValueId, $sectionId, $elementId, $property, $operator, $value);

            $this->productRepository->update($product);
        }
    }

    /**
     * @param RemoveCondition $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleRemoveProductCondition(RemoveCondition $command)
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
     * @param CopyCondition $command
     *
     * @return void
     * @throws InvalidUuidException
     */
    public function handleCopyProductCondition(CopyCondition $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $product->copyCondition(
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
        yield AddConditionSet::class => [
            'method' => 'handleAddConditionSet',
            'bus' => 'command_bus'
        ];

        yield UpdateConditionSet::class => [
            'method' => 'handleUpdateConditionSet',
            'bus' => 'command_bus'
        ];

        yield RemoveConditionSet::class => [
            'method' => 'handleRemoveConditionSet',
            'bus' => 'command_bus'
        ];

        yield AddConditionSetCondition::class => [
            'method' => 'handleAddConditionSetCondition',
            'bus' => 'command_bus'
        ];

        yield UpdateConditionSetCondition::class => [
            'method' => 'handleUpdateConditionSetCondition',
            'bus' => 'command_bus'
        ];

        yield CopyConditionSetCondition::class => [
            'method' => 'handleCopyConditionSetCondition',
            'bus' => 'command_bus'
        ];

        yield RemoveConditionSetCondition::class => [
            'method' => 'handleRemoveConditionSetCondition',
            'bus' => 'command_bus'
        ];

        yield AddCondition::class => [
            'method' => 'handleAddProductCondition',
            'bus' => 'command_bus'
        ];

        yield UpdateCondition::class => [
            'method' => 'handleUpdateProductCondition',
            'bus' => 'command_bus'
        ];

        yield RemoveCondition::class => [
            'method' => 'handleRemoveProductCondition',
            'bus' => 'command_bus'
        ];

        yield CopyCondition::class => [
            'method' => 'handleCopyProductCondition',
            'bus' => 'command_bus'
        ];
    }
}
