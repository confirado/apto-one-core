<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Rule;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Application\Backend\Commands\Product\ProductChildHandler;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
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
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     * @throws \Apto\Catalog\Domain\Core\Model\Product\Rule\RuleCriterionInvalidOperatorException
     * @throws \Apto\Catalog\Domain\Core\Model\Product\Rule\RuleCriterionInvalidPropertyException
     * @throws \Apto\Catalog\Domain\Core\Model\Product\Rule\RuleCriterionInvalidValueException
     */
    public function handleAddProductRuleImplication(AddProductRuleImplication $command)
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

        yield AddProductRuleCondition::class => [
            'method' => 'handleAddProductRuleCondition',
            'bus' => 'command_bus'
        ];

        yield AddProductRuleImplication::class => [
            'method' => 'handleAddProductRuleImplication',
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
    }
}
