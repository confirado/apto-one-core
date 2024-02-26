<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Rule;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Backend\Commands\Product\ProductChildHandler;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
use Apto\Catalog\Domain\Core\Model\Product\Rule\RuleCriterion;
use Apto\Catalog\Domain\Core\Model\Product\Rule\RuleCriterionInvalidOperatorException;
use Apto\Catalog\Domain\Core\Model\Product\Rule\RuleCriterionInvalidPropertyException;
use Apto\Catalog\Domain\Core\Model\Product\Rule\RuleCriterionInvalidTypeException;
use Apto\Catalog\Domain\Core\Model\Product\Rule\RuleCriterionInvalidValueException;
use Apto\Catalog\Domain\Core\Model\Product\Rule\RuleCriterionOperator;

class ProductRuleHandler extends ProductChildHandler
{
    /**
     * @param AddProductRule $command
     */
    public function handleAddProductRule(AddProductRule $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $product->addRule(
                $command->getRuleName()
            );

            $this->productRepository->update($product);
        }
    }

    /**
     * @param UpdateProductRule $command
     */
    public function handleUpdateProductRule(UpdateProductRule $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $ruleId = new AptoUuid($command->getRuleId());

            $product
                ->setRuleActive($ruleId, $command->getActive())
                ->setRuleName($ruleId, $command->getRuleName())
                ->setRuleErrorMessage(
                    $ruleId,
                    $this->getTranslatedValue(
                        $command->getErrorMessage()
                    )
                )
                ->setRuleConditionsOperator($ruleId, $command->getConditionsOperator())
                ->setRuleImplicationsOperator($ruleId, $command->getImplicationsOperator())
                ->setSoftRule($ruleId, $command->getSoftRule())
                ->setRuleDescription($ruleId, $command->getDescription());

            $this->productRepository->update($product);
        }
    }

    /**
     * @param RemoveProductRule $command
     */
    public function handleRemoveProductRule(RemoveProductRule $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $product->removeRule(
                new AptoUuid(
                    $command->getRuleId()
                )
            );

            $this->productRepository->update($product);
        }
    }

    /**
     * @param CopyProductRule $command
     *
     * @return void
     * @throws InvalidUuidException
     * @throws RuleCriterionInvalidOperatorException
     * @throws RuleCriterionInvalidPropertyException
     * @throws RuleCriterionInvalidTypeException
     * @throws RuleCriterionInvalidValueException
     */
    public function handleCopyProductRule(CopyProductRule $command): void
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $product->copyRule(
                new AptoUuid($command->getRuleId()),
            );

            $this->productRepository->update($product);
        }
    }

    /**
     * @param AddProductRuleCondition $command
     */
    public function handleAddProductRuleCondition(AddProductRuleCondition $command)
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
            $product->addRuleCondition(
                new AptoUuid($command->getRuleId()),
                new RuleCriterionOperator($command->getOperator()),
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
     * @param AddProductRuleImplication $command
     * @return void
     * @throws InvalidUuidException
     * @throws RuleCriterionInvalidOperatorException
     * @throws RuleCriterionInvalidPropertyException
     * @throws RuleCriterionInvalidValueException
     */
    public function handleAddProductRuleImplication(AddProductRuleImplication $command): void
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
            $product->addRuleImplication(
                new AptoUuid($command->getRuleId()),
                new RuleCriterionOperator($command->getOperator()),
                $command->getType(),
                new AptoUuid($command->getSectionId()),
                null !== $command->getElementId() ? new AptoUuid($command->getElementId()) : null,
                $command->getProperty(),
                $computedProductValue,
                $command->getValue()
            );

            $this->productRepository->update($product);
        }
    }

    /**
     * @param UpdateProductRuleCondition $command
     *
     * @return void
     * @throws InvalidUuidException
     * @throws RuleCriterionInvalidOperatorException
     */
    public function handleUpdateProductRuleCondition(UpdateProductRuleCondition $command): void
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $ruleId = new AptoUuid($command->getRuleId());
            $conditionId = new AptoUuid($command->getConditionId());
            $rule = $product->getRule($ruleId);

            // type is required in both cases, so we don't check if it is there or not
            $rule->updateConditionType($conditionId, $command->getType());

            if ($command->getType() === RuleCriterion::STANDARD_TYPE) {
                $rule->updateConditionSectionId($conditionId, $command->getSectionId());
                $rule->updateConditionElementId($conditionId,  $command->getElementId());
                $rule->updateConditionProperty($conditionId, $command->getProperty());
            }
            else if ($command->getType() === RuleCriterion::COMPUTED_VALUE_TYPE) {
                $rule->updateConditionComputedValue($conditionId, $product->getComputedProductValue($command->getComputedValueId()));
            }

            // operator is required in both cases
            $rule->updateConditionOperator($conditionId, new RuleCriterionOperator($command->getOperator()));

            // value can be null, we might want to unset the value
            $rule->updateConditionValue($conditionId, $command->getValue());

            $this->productRepository->update($product);
        }
    }

    /**
     * @param CopyProductRuleCondition $command
     *
     * @return void
     * @throws InvalidUuidException
     * @throws RuleCriterionInvalidOperatorException
     * @throws RuleCriterionInvalidPropertyException
     * @throws RuleCriterionInvalidTypeException
     * @throws RuleCriterionInvalidValueException
     */
    public function handleCopyProductRuleCondition(CopyProductRuleCondition $command): void
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $product->copyRuleCondition(
                new AptoUuid($command->getRuleId()),
                new AptoUuid($command->getConditionId())
            );
            $this->productRepository->update($product);
        }
    }

    /**
     * @param RemoveProductRuleCondition $command
     */
    public function handleRemoveProductRuleCondition(RemoveProductRuleCondition $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $product->removeRuleCondition(
                new AptoUuid($command->getRuleId()),
                new AptoUuid($command->getConditionId())
            );

            $this->productRepository->update($product);
        }
    }

    /**
     * @param UpdateProductRuleImplication $command
     *
     * @return void
     * @throws InvalidUuidException
     * @throws RuleCriterionInvalidOperatorException
     */
    public function handleUpdateProductRuleImplication(UpdateProductRuleImplication $command): void
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $ruleId = new AptoUuid($command->getRuleId());
            $implicationId = new AptoUuid($command->getImplicationId());
            $rule = $product->getRule($ruleId);

            // type is required in both cases, so we don't check if it is there or not
            $rule->updateImplicationType($implicationId, $command->getType());

            if ($command->getType() === RuleCriterion::STANDARD_TYPE) {
                $rule->updateImplicationSectionId($implicationId, $command->getSectionId());
                $rule->updateImplicationElementId($implicationId,  $command->getElementId());
                $rule->updateImplicationProperty($implicationId, $command->getProperty());
            }
            else if ($command->getType() === RuleCriterion::COMPUTED_VALUE_TYPE) {
                $rule->updateImplicationComputedValue($implicationId, $product->getComputedProductValue($command->getComputedValueId()));
            }

            // operator is required in both cases
            $rule->updateImplicationOperator($implicationId, new RuleCriterionOperator($command->getOperator()));

            // value can be null, we might want to unset the value
            $rule->updateImplicationValue($implicationId, $command->getValue());

            $this->productRepository->update($product);
        }
    }

    /**
     * @param CopyProductRuleImplication $command
     *
     * @return void
     * @throws InvalidUuidException
     * @throws RuleCriterionInvalidOperatorException
     * @throws RuleCriterionInvalidPropertyException
     * @throws RuleCriterionInvalidTypeException
     * @throws RuleCriterionInvalidValueException
     */
    public function handleCopyProductRuleImplication(CopyProductRuleImplication $command): void
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $product->copyRuleImplication(
                new AptoUuid($command->getRuleId()),
                new AptoUuid($command->getImplicationId()),
            );

            $this->productRepository->update($product);
        }
    }

    /**
     * @param RemoveProductRuleImplication $command
     */
    public function handleRemoveProductRuleImplication(RemoveProductRuleImplication $command)
    {
        $product = $this->productRepository->findById($command->getProductId());

        if (null !== $product) {
            $product->removeRuleImplication(
                new AptoUuid($command->getRuleId()),
                new AptoUuid($command->getImplicationId())
            );

            $this->productRepository->update($product);
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddProductRule::class => [
            'method' => 'handleAddProductRule',
            'bus' => 'command_bus'
        ];

        yield UpdateProductRule::class => [
            'method' => 'handleUpdateProductRule',
            'bus' => 'command_bus'
        ];

        yield RemoveProductRule::class => [
            'method' => 'handleRemoveProductRule',
            'bus' => 'command_bus'
        ];

        yield CopyProductRule::class => [
            'method' => 'handleCopyProductRule',
            'bus' => 'command_bus'
        ];

        yield AddProductRuleCondition::class => [
            'method' => 'handleAddProductRuleCondition',
            'bus' => 'command_bus'
        ];

        yield AddProductRuleImplication::class => [
            'method' => 'handleAddProductRuleImplication',
            'bus' => 'command_bus'
        ];

        yield UpdateProductRuleCondition::class => [
            'method' => 'handleUpdateProductRuleCondition',
            'bus' => 'command_bus'
        ];

        yield CopyProductRuleCondition::class => [
            'method' => 'handleCopyProductRuleCondition',
            'bus' => 'command_bus'
        ];

        yield RemoveProductRuleCondition::class => [
            'method' => 'handleRemoveProductRuleCondition',
            'bus' => 'command_bus'
        ];

        yield RemoveProductRuleImplication::class => [
            'method' => 'handleRemoveProductRuleImplication',
            'bus' => 'command_bus'
        ];

        yield UpdateProductRuleImplication::class => [
            'method' => 'handleUpdateProductRuleImplication',
            'bus' => 'command_bus'
        ];

        yield CopyProductRuleImplication::class => [
            'method' => 'handleCopyProductRuleImplication',
            'bus' => 'command_bus'
        ];

        yield RemoveProductRuleImplication::class => [
            'method' => 'handleRemoveProductRuleImplication',
            'bus' => 'command_bus'
        ];
    }
}
