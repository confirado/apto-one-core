<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPriceDuplicateException;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\Category\Category;
use Apto\Catalog\Domain\Core\Model\Category\CategoryRepository;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidOperatorException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidPropertyException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidValueException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionOperator;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Part;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\PartRepository;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\Quantity;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\QuantityCalculation;
use Apto\Plugins\PartsList\Domain\Core\Model\Unit\Unit;
use Apto\Plugins\PartsList\Domain\Core\Model\Unit\UnitRepository;
use Exception;
use Money\Currency;
use Money\Money;

class PartCommandHandler extends AbstractCommandHandler
{
    /**
     * @var PartRepository
     */
    private $partRepository;

    /**
     * @var UnitRepository
     */
    private $unitRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CategoryRepository
     */
    private CategoryRepository $categoryRepository;

    /**
     * PartCommandHandler constructor.
     * @param PartRepository $partRepository
     * @param UnitRepository $unitRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(PartRepository $partRepository, UnitRepository $unitRepository, ProductRepository $productRepository, CategoryRepository $categoryRepository)
    {
        $this->partRepository = $partRepository;
        $this->unitRepository = $unitRepository;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param AddPart $command
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    public function handleAddPart(AddPart $command)
    {
        // create new part
        $part = new Part(
            $this->partRepository->nextIdentity(),
            $command->getActive(),
            $this->getTranslatedValue(
                $command->getName()
            )
        );

        // set part properties
        $part
            ->setPartNumber(
                $command->getPartNumber()
            )
            ->setUnit(
                $this->getUnit($command->getUnitId())
            )
            ->setDescription(
                $this->getTranslatedValue($command->getDescription())
            )
            ->setCategory($this->getCategory($command->getCategoryId()));

        // add part and publish fired events
        $this->partRepository->add($part);
        $part->publishEvents();
    }

    /**
     * @param UpdatePart $command
     */
    public function handleUpdatePart(UpdatePart $command)
    {
        // find part to update
        $part = $this->partRepository->findById($command->getId());

        // return if no part was found
        if (null === $part) {
            return;
        }

        // set part properties
        $part
            ->setActive(
                $command->getActive()
            )
            ->setPartNumber(
                $command->getPartNumber()
            )
            ->setUnit(
                $this->getUnit($command->getUnitId())
            )
            ->setName(
                $this->getTranslatedValue($command->getName())
            )
            ->setDescription(
                $this->getTranslatedValue($command->getDescription())
            )
            ->setCategory($this->getCategory($command->getCategoryId()));

        // update part and publish fired events
        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param RemovePart $command
     */
    public function handleRemovePart(RemovePart $command)
    {
        $part = $this->partRepository->findById($command->getId());

        if (null === $part) {
            return;
        }

        $this->partRepository->remove($part);
    }

    /**
     * @param AddProductUsage $command
     * @throws InvalidUuidException
     */
    public function handleAddProductUsage(AddProductUsage $command)
    {
        $part = $this->partRepository->findById($command->getPartId());
        $product = $this->productRepository->findById($command->getProductId());

        if (null === $part) {
            return;
        }

        $part
            ->addProductUsage(
                new AptoUuid(
                    $command->getUsageForUuid()
                ),
                new Quantity(
                    $command->getQuantity()
                )
            )
            ->addPartProductAssociation(
                $product
            );

        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param AddSectionUsage $command
     * @throws InvalidUuidException
     */
    public function handleAddSectionUsage(AddSectionUsage $command)
    {
        $part = $this->partRepository->findById($command->getPartId());
        $product = $this->productRepository->findById($command->getProductId());
        $productId = new AptoUuid(
            $command->getProductId()
        );
        if (null === $part) {
            return;
        }

        $part
            ->addSectionUsage(
                new AptoUuid(
                    $command->getUsageForUuid()
                ),
                new Quantity(
                    $command->getQuantity()
                ), $productId
            )
            ->addPartProductAssociation(
                $product
            );

        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param AddElementUsage $command
     * @throws InvalidUuidException
     */
    public function handleAddElementUsage(AddElementUsage $command)
    {
        $part = $this->partRepository->findById($command->getPartId());
        $product = $this->productRepository->findById($command->getProductId());
        $productId = new AptoUuid(
            $command->getProductId()
        );
        if (null === $part) {
            return;
        }

        $part
            ->addElementUsage(
                new AptoUuid(
                    $command->getUsageForUuid()
                ),
                new Quantity(
                    $command->getQuantity()
                ), $productId
            )
            ->addPartProductAssociation(
                $product
            );

        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param AddRuleUsage $command
     * @return void
     */
    public function handleAddRuleUsage(AddRuleUsage $command)
    {
        $part = $this->partRepository->findById($command->getPartId());

        if (null === $part) {
            return;
        }

        $part->addRuleUsage(
            $command->getname(),
            new Quantity(
                $command->getQuantity()
            )
        );

        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param UpdateProductUsageQuantity $command
     * @throws InvalidUuidException
     */
    public function handleUpdateProductUsageQuantity(UpdateProductUsageQuantity $command)
    {
        $part = $this->partRepository->findById($command->getPartId());

        if (null === $part) {
            return;
        }

        $part->setProductUsageQuantity(
            new AptoUuid(
                $command->getUsageId()
            ),
            new Quantity(
                $command->getQuantity()
            )
        );

        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param UpdateSectionUsageQuantity $command
     * @throws InvalidUuidException
     */
    public function handleUpdateSectionUsageQuantity(UpdateSectionUsageQuantity $command)
    {
        $part = $this->partRepository->findById($command->getPartId());

        if (null === $part) {
            return;
        }

        $part->setSectionUsageQuantity(
            new AptoUuid(
                $command->getUsageId()
            ),
            new Quantity(
                $command->getQuantity()
            )
        );

        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param UpdateElementUsage $command
     * @throws InvalidUuidException
     */
    public function handleUpdateElementUsage(UpdateElementUsage $command)
    {
        $part = $this->partRepository->findById($command->getPartId());

        if (null === $part) {
            return;
        }

        $part->setElementUsageQuantity(
            new AptoUuid(
                $command->getUsageId()
            ),
            new Quantity(
                $command->getQuantity()
            )
        );

        $part->setElementUsageQuantityCalculation(
            new AptoUuid(
                $command->getUsageId()
            ),
            new QuantityCalculation(
                $command->getQuantityCalculation()['active'],
                $command->getQuantityCalculation()['operation'],
                $command->getQuantityCalculation()['fieldType'],
                $command->getQuantityCalculation()['field'],
                $command->getQuantityCalculation()['fieldPosition']
            )
        );

        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param UpdateRuleUsage $command
     * @throws InvalidUuidException
     */
    public function handleUpdateRuleUsage(UpdateRuleUsage $command)
    {
        $part = $this->partRepository->findById($command->getPartId());

        if (null === $part) {
            return;
        }

        $part->setRuleUsageQuantity(
            new AptoUuid(
                $command->getUsageId()
            ),
            new Quantity(
                $command->getQuantity()
            )
        );

        $part->setRuleUsageName(
            new AptoUuid(
                $command->getUsageId()
            ),
            $command->getName()
        );

        $part->setRuleUsageConditionsOperator(
            new AptoUuid(
                $command->getUsageId()
            ),
            $command->getOperator()
        );

        $part->setRuleUsageActive(
            new AptoUuid(
                $command->getUsageId()
            ),
            $command->getActive()
        );

        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param AddRuleUsageCondition $command
     * @throws InvalidUuidException
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidValueException
     */
    public function handleAddRuleUsageCondition(AddRuleUsageCondition $command)
    {
        $part = $this->partRepository->findById($command->getPartId());
        if (null === $part) {
            return;
        }
        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            return;
        }
        $part
            ->addRuleUsageCondition(
                new AptoUuid($command->getUsageId()),
                $product,
                new CriterionOperator($command->getOperator()),
                $command->getValue(),
                $command->getSectionId(),
                $command->getElementId(),
                $command->getProperty(),
                null,
                $command->getComputedValueId()
            )
            ->addPartProductAssociation(
                $product
            );

        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param RemoveRuleUsageCondition $command
     * @throws InvalidUuidException
     */
    public function handleRemoveRuleUsageCondition(RemoveRuleUsageCondition $command)
    {
        $part = $this->partRepository->findById($command->getPartId());
        if (null === $part) {
            return;
        }

        $part->removeRuleUsageCondition(
            new AptoUuid($command->getUsageId()),
            new AptoUuid($command->getConditionId())
        );

        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param UpdateRuleUsageCondition $command
     * @throws InvalidUuidException
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidValueException
     * @throws Exception
     */
    public function handleUpdateRuleUsageCondition(UpdateRuleUsageCondition $command)
    {
        $part = $this->partRepository->findById($command->getPartId());
        if (null === $part) {
            return;
        }

        $product = $this->productRepository->findById($command->getProductId());
        if (null === $product) {
            return;
        }

        $part->removeRuleUsageCondition(
            new AptoUuid($command->getUsageId()),
            new AptoUuid($command->getConditionId())
        );

        $part->addRuleUsageCondition(
            new AptoUuid($command->getUsageId()),
            $product,
            new CriterionOperator($command->getOperator()),
            $command->getValue(),
            $command->getSectionId(),
            $command->getElementId(),
            $command->getProperty(),
            new AptoUuid($command->getConditionId()),
            $command->getComputedValueId()
        );

        $part->addPartProductAssociation($product);
        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param RemoveProductUsage $command
     * @throws InvalidUuidException
     * @throws Exception
     */
    public function handleRemoveProductUsage(RemoveProductUsage $command)
    {
        $part = $this->partRepository->findById($command->getPartId());
        $productId = $part->getProductUsage(new AptoUuid($command->getUsageId()))->getProductId();

        if (null === $part) {
            return;
        }

        $part
            ->removeProductUsage(
                new AptoUuid(
                    $command->getUsageId()
                )
            )
            ->removePartProductAssociation(new AptoUuid($productId)
            );

        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param RemoveSectionUsage $command
     * @throws InvalidUuidException
     * @throws Exception
     */
    public function handleRemoveSectionUsage(RemoveSectionUsage $command)
    {
        $part = $this->partRepository->findById($command->getPartId());
        $productId = new AptoUuid($part->getSectionUsage(new AptoUuid($command->getUsageId()))->getProductId());

        if (null === $part) {
            return;
        }

        $part
            ->removeSectionUsage(
                new AptoUuid(
                    $command->getUsageId()
                )
            )
            ->removePartProductAssociation(new AptoUuid($productId)
            );


        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param RemoveElementUsage $command
     * @throws InvalidUuidException
     * @throws Exception
     */
    public function handleRemoveElementUsage(RemoveElementUsage $command)
    {
        $part = $this->partRepository->findById($command->getPartId());
        $productId = new AptoUuid($part->getElementUsage(new AptoUuid($command->getUsageId()))->getProductId());

        if (null === $part) {
            return;
        }

        $part->
            removeElementUsage(
                new AptoUuid(
                    $command->getUsageId()
                )
            )
            ->removePartProductAssociation(new AptoUuid($productId)
            );

        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param RemoveRuleUsage $command
     * @throws InvalidUuidException
     * @throws Exception
     */
    public function handleRemoveRuleUsage(RemoveRuleUsage $command)
    {
        $part = $this->partRepository->findById($command->getPartId());

        if (null === $part) {
            return;
        }

        $part->removeRuleUsage(
            new AptoUuid(
                $command->getUsageId()
            )
        );

        $this->partRepository->update($part);
        $part->publishEvents();
    }

    /**
     * @param AddPartPrice $command
     * @throws InvalidUuidException
     * @throws AptoPriceDuplicateException
     */
    public function handleAddPartPrice(AddPartPrice $command)
    {
        $part = $this->partRepository->findById($command->getId());
        if (null !== $part) {
            $part->addAptoPrice(
                new Money(
                    $command->getAmount(),
                    new Currency(
                        $command->getCurrency()
                    )
                ),
                new AptoUuid(
                    $command->getCustomerGroupId()
                )
            );
            $this->partRepository->update($part);
            $part->publishEvents();
        }
    }

    /**
     * @param UpdatePartPrice $command
     * @throws AptoPriceDuplicateException
     * @throws InvalidUuidException
     */
    public function handleUpdatePartPrice(UpdatePartPrice $command)
    {
        $part = $this->partRepository->findById($command->getId());
        if (null !== $part) {
            $priceId = new AptoUuid($command->getPriceId());
            $part
                ->setAptoPricePrice(
                    $priceId,
                    new Money(
                        $command->getAmount(),
                        new Currency(
                            $command->getCurrency()
                        )
                    )
                )
                ->setAptoPriceCustomerGroupId(
                    $priceId,
                    new AptoUuid(
                        $command->getCustomerGroupId()
                    )
                );
            $this->partRepository->update($part);
            $part->publishEvents();
        }
    }

    /**
     * @param RemovePartPrice $command
     * @throws InvalidUuidException
     */
    public function handleRemovePartPrice(RemovePartPrice $command)
    {
        $part = $this->partRepository->findById($command->getId());
        if (null !== $part) {
            $part->removeAptoPrice(
                new AptoUuid(
                    $command->getPriceId()
                )
            );
            $this->partRepository->update($part);
            $part->publishEvents();
        }
    }


    /**
     * @param string|null $unitId
     * @return Unit|null
     */
    protected function getUnit(?string $unitId): ?Unit
    {
        $unit = null;
        if (null !== $unitId) {
            $unit = $this->unitRepository->findById($unitId);
        }
        return $unit;
    }

    /**
     * @param string|null $categoryId
     * @return Category|null
     */
    protected function getCategory(?string $categoryId): ?Category
    {
        return $categoryId ? $this->categoryRepository->findById($categoryId) : null;
    }

    /**
     * @param int|null $amount
     * @param string|null $currencyCode
     * @return Money
     */
    protected function getPrice(?int $amount, ?string $currencyCode): Money
    {
        if (null === $amount || null === $currencyCode) {
            return new Money(0, new Currency('EUR'));
        }

        return new Money($amount, new Currency($currencyCode));
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddPart::class => [
            'method' => 'handleAddPart',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield UpdatePart::class => [
            'method' => 'handleUpdatePart',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield RemovePart::class => [
            'method' => 'handleRemovePart',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield AddProductUsage::class => [
            'method' => 'handleAddProductUsage',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield AddSectionUsage::class => [
            'method' => 'handleAddSectionUsage',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield AddElementUsage::class => [
            'method' => 'handleAddElementUsage',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield AddRuleUsage::class => [
            'method' => 'handleAddRuleUsage',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield UpdateProductUsageQuantity::class => [
            'method' => 'handleUpdateProductUsageQuantity',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield UpdateSectionUsageQuantity::class => [
            'method' => 'handleUpdateSectionUsageQuantity',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield UpdateElementUsage::class => [
            'method' => 'handleUpdateElementUsage',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield UpdateRuleUsage::class => [
            'method' => 'handleUpdateRuleUsage',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield AddRuleUsageCondition::class => [
            'method' => 'handleAddRuleUsageCondition',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield RemoveRuleUsageCondition::class => [
            'method' => 'handleRemoveRuleUsageCondition',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield UpdateRuleUsageCondition::class => [
            'method' => 'handleUpdateRuleUsageCondition',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield RemoveProductUsage::class => [
            'method' => 'handleRemoveProductUsage',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield RemoveSectionUsage::class => [
            'method' => 'handleRemoveSectionUsage',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield RemoveElementUsage::class => [
            'method' => 'handleRemoveElementUsage',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield RemoveRuleUsage::class => [
            'method' => 'handleRemoveRuleUsage',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield AddPartPrice::class => [
            'method' => 'handleAddPartPrice',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield UpdatePartPrice::class => [
            'method' => 'handleUpdatePartPrice',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield RemovePartPrice::class => [
            'method' => 'handleRemovePartPrice',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];
    }
}
