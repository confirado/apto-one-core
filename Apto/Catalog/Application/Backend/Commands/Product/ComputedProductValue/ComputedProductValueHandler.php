<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\ComputedProductValue;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Application\Backend\Commands\Product\ProductChildHandler;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\ComputedProductValueCalculator;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\AliasNotUniqueException;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValueNameNotValidException;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\InvalidAliasException;
use Apto\Catalog\Domain\Core\Model\Product\InvalidComputedValueNameException;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;

class ComputedProductValueHandler extends ProductChildHandler
{
    /**
     * @var ComputedProductValueCalculator
     */
    private $computedProductValueCalculator;

    /**
     * @param ProductRepository $productRepository
     * @param ComputedProductValueCalculator $computedProductValueCalculator
     */
    public function __construct(ProductRepository $productRepository, ComputedProductValueCalculator $computedProductValueCalculator)
    {
        parent::__construct($productRepository);
        $this->computedProductValueCalculator = $computedProductValueCalculator;

    }

    /**
     * @param AddComputedProductValue $command
     * @return void
     * @throws InvalidComputedValueNameException
     */
    public function handleAddComputedProductValue(AddComputedProductValue $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if ($product === null) {
            return;
        }
        $product->addComputedProductValue(
            new ComputedProductValue(
                new AptoUuid(),
                $command->getName(),
                $product
            )
        );
        $this->productRepository->update($product);
    }

    /**
     * @param UpdateComputedProductValue $command
     * @return void
     * @throws CircularReferenceException
     * @throws ComputedProductValueNameNotValidException
     */
    public function handleUpdateComputedProductValue(UpdateComputedProductValue $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if ($product === null) {
            return;
        }

        $computedProductValues = $product->getComputedProductValues();
        /* @var ComputedProductValue $computedProductValue */
        foreach ($computedProductValues as $computedProductValue) {
            if ($computedProductValue->getId()->getId() === $command->getComputedValueId()) {
                $computedProductValue->setName($command->getName());
                $computedProductValue->setFormula($command->getFormula());
                break;
            }
        }

        $this->computedProductValueCalculator->checkForCircularReference($computedProductValues->getValues());
        $product->setComputedProductValues($computedProductValues);
        $this->productRepository->update($product);
    }

    /**
     * @param AddAlias $command
     * @return void
     * @throws ComputedProductValueNameNotValidException
     * @throws InvalidAliasException
     * @throws AliasNotUniqueException
     */
    public function handleAddAlias(AddAlias $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if ($product === null) {
            return;
        }

        $computedProductValues = $product->getComputedProductValues();

        /* @var ComputedProductValue $computedProductValue */
        foreach ($computedProductValues as &$computedProductValue) {
            if ($computedProductValue->getId()->getId() === $command->getComputedProductValueId()) {
                $computedProductValue = $computedProductValue->addAlias(
                    $command->getSectionId(),
                    $command->getElementId(),
                    $command->getName(),
                    $command->getProperty(),
                    $command->isCP()
                );
            }
        }
        $product->setComputedProductValues($computedProductValues);
        $this->productRepository->update($product);
    }

    /**
     * @param RemoveComputedProductValue $command
     */
    public function handleRemoveComputedProductValue(RemoveComputedProductValue $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if ($product === null) {
            return;
        }
        $product->removeComputedProductValue($command->getId());
        $this->productRepository->update($product);
    }

    /**
     * @param RemoveAlias $command
     */
    public function handleRemoveAlias(RemoveAlias $command)
    {
        $product = $this->productRepository->findById($command->getProductId());
        if ($product === null) {
            return;
        }
        /* @var ComputedProductValue $computedProductValue */
        $computedProductValues = $product->getComputedProductValues();
        foreach ($computedProductValues as &$computedProductValue) {
            if ($computedProductValue->getId()->getId() === $command->getComputedProductValueId()) {
                $computedProductValue->removeAlias($command->getId());
            }
        }
        $product->setComputedProductValues($computedProductValues);
        $this->productRepository->update($product);
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddComputedProductValue::class => [
            'method' => 'handleAddComputedProductValue',
            'bus' => 'command_bus',
            'aptoMessageName' => 'AddProductComputedProductValue'
        ];

        yield UpdateComputedProductValue::class => [
            'method' => 'handleUpdateComputedProductValue',
            'bus' => 'command_bus',
            'aptoMessageName' => 'UpdateProductComputedProductValue'
        ];

        yield AddAlias::class => [
            'method' => 'handleAddAlias',
            'bus' => 'command_bus',
            'aptoMessageName' => 'AddProductComputedProductValueAlias'
        ];

        yield RemoveComputedProductValue::class => [
            'method' => 'handleRemoveComputedProductValue',
            'bus' => 'command_bus',
            'aptoMessageName' => 'RemoveProductComputedProductValue'
        ];

        yield RemoveAlias::class => [
            'method' => 'handleRemoveAlias',
            'bus' => 'command_bus',
            'aptoMessageName' => 'RemoveProductComputedProductValueAlias'
        ];
    }
}