<?php

namespace Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEvent;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventSubscriber;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Catalog\Domain\Core\Model\Product\ProductCopied;
use Apto\Catalog\Domain\Core\Model\Product\ProductElementCopied;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;
use Apto\Catalog\Domain\Core\Model\Product\ProductSectionCopied;
use Apto\Catalog\Domain\Core\Model\Product\Rule\Rule;
use Apto\Catalog\Domain\Core\Model\Product\Rule\RuleCondition;
use Apto\Catalog\Domain\Core\Model\Product\Rule\RuleImplication;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\Product\Element\SelectBoxElementDefinition;
use Apto\Base\Domain\Core\Model\InvalidUuidException;

class CopyProductEventSubscriber implements DomainEventSubscriber
{

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var SelectBoxItemRepository
     */
    protected $selectBoxItemRepository;

    /**
     * CopyProductEventSubscriber constructor.
     * @param ProductRepository $productRepository
     * @param SelectBoxItemRepository $selectBoxItemRepository
     */
    public function __construct(ProductRepository $productRepository, SelectBoxItemRepository $selectBoxItemRepository)
    {
        $this->productRepository = $productRepository;
        $this->selectBoxItemRepository = $selectBoxItemRepository;
    }

    /**
     * @param DomainEvent $event
     * @throws InvalidUuidException
     */
    public function handle(DomainEvent $event)
    {
        if ($event instanceof ProductCopied) {
            $this->handleProductCopied($event);
        }

        if ($event instanceof ProductSectionCopied) {
            $this->handleProductSectionCopied($event);
        }

        if ($event instanceof ProductElementCopied) {
            $this->handleProductElementCopied($event);
        }
    }

    /**
     * @param DomainEvent $event
     * @throws InvalidUuidException
     */
    private function handleProductCopied(DomainEvent $event)
    {
        /** @var ProductCopied $event */
        $id = $event->getProductId();
        $entityMapping = $event->getEntityMapping();

        // get copied product
        $copiedProduct = $this->getCopiedProduct($id, $entityMapping);
        if (null === $copiedProduct) {
            return;
        }

        // get related selectBoxItems
        $selectBoxItems = $this->selectBoxItemRepository->findByProductId($id->getId());
        $itemMapping = [];
        $defaultItemMapping = [];

        // copy all selectBoxItems
        $this->copySelectBoxItems($selectBoxItems, $entityMapping, $itemMapping, $defaultItemMapping);
        $this->updateDefaultItems($copiedProduct, $defaultItemMapping);
        $this->updateProductRules($copiedProduct, $itemMapping);

        // update product model
        $this->productRepository->update($copiedProduct);
        $this->productRepository->flush($copiedProduct);
    }

    /**
     * @param DomainEvent $event
     * @throws InvalidUuidException
     */
    private function handleProductSectionCopied(DomainEvent $event)
    {
        /** @var ProductSectionCopied $event */
        $id = $event->getId();
        $sectionId = $event->getSectionId();
        $entityMapping = $event->getEntityMapping();

        // get product product
        $product = $this->productRepository->findById($id->getId());
        if (null === $product) {
            return;
        }

        // get related selectBoxItems
        $selectBoxItems = $this->selectBoxItemRepository->findBySectionId($sectionId->getId());
        $itemMapping = [];
        $defaultItemMapping = [];

        // copy all selectBoxItems
        $this->copySelectBoxItems($selectBoxItems, $entityMapping, $itemMapping, $defaultItemMapping);
        $this->updateDefaultItems($product, $defaultItemMapping);

        // update product model
        $this->productRepository->update($product);
        $this->productRepository->flush($product);
    }

    /**
     * @param DomainEvent $event
     * @throws InvalidUuidException
     */
    private function handleProductElementCopied(DomainEvent $event)
    {
        /** @var ProductElementCopied $event */
        $id = $event->getId();
        $elementId = $event->getElementId();
        $entityMapping = $event->getEntityMapping();

        // get product product
        $product = $this->productRepository->findById($id->getId());
        if (null === $product) {
            return;
        }

        // get related selectBoxItems
        $selectBoxItems = $this->selectBoxItemRepository->findByElementId($elementId->getId());
        $itemMapping = [];
        $defaultItemMapping = [];

        // copy all selectBoxItems
        $this->copySelectBoxItems($selectBoxItems, $entityMapping, $itemMapping, $defaultItemMapping);
        $this->updateDefaultItems($product, $defaultItemMapping);

        // update product model
        $this->productRepository->update($product);
        $this->productRepository->flush($product);
    }

    /**
     * @param array $selectBoxItems
     * @param array $entityMapping
     * @param array $itemMapping
     * @param array $defaultItemMapping
     * @throws InvalidUuidException
     */
    private function copySelectBoxItems(array $selectBoxItems, array $entityMapping, array &$itemMapping, array &$defaultItemMapping)
    {
        // copy all selectBoxItems
        foreach ($selectBoxItems as $selectBoxItem) {
            /** @var SelectBoxItem $selectBoxItem */
            // check mapping is valid
            if (
                !array_key_exists($selectBoxItem->getProductId()->getId(), $entityMapping) ||
                !array_key_exists($selectBoxItem->getSectionId()->getId(), $entityMapping) ||
                !array_key_exists($selectBoxItem->getElementId()->getId(), $entityMapping)
            ) {
                continue;
            }

            // copy select box item
            $copiedSelectBoxItem = $selectBoxItem->copy(
                $this->selectBoxItemRepository->nextIdentity(),
                $entityMapping[$selectBoxItem->getProductId()->getId()],
                $entityMapping[$selectBoxItem->getSectionId()->getId()],
                $entityMapping[$selectBoxItem->getElementId()->getId()]
            );

            $itemMapping[$selectBoxItem->getId()->getId()] = $copiedSelectBoxItem;
            if ($selectBoxItem->getIsDefault()) {
                $defaultItemMapping[$selectBoxItem->getId()->getId()] = $copiedSelectBoxItem;
            }

            // add new select box item
            $this->selectBoxItemRepository->add($copiedSelectBoxItem);
        }
    }

    /**
     * @param Product $copiedProduct
     * @param array $defaultItemMapping
     */
    private function updateDefaultItems(Product $copiedProduct, array $defaultItemMapping)
    {
        /** @var  SelectBoxItem $copiedDefaultSelectBoxItem */
        foreach ($defaultItemMapping as $oldItemId => $copiedDefaultSelectBoxItem) {
            // get element definition
            $elementDefinition = $copiedProduct->getElementDefinition(
                $copiedDefaultSelectBoxItem->getSectionId(),
                $copiedDefaultSelectBoxItem->getElementId()
            );

            // because not all select box items are active we must filter out those elements that are not an select box anymore
            // this will be the case if you change a select box element with a default item to e.g. a default element, select box items will not be removed in that case
            // @todo can be removed if the case above will be fixed
            if (get_class($elementDefinition) !== SelectBoxElementDefinition::class) {
                continue;
            }

            // update element definition default item
            $elementDefinitionJson = $elementDefinition->jsonEncode();
            $copiedDefaultItem = [
                'id' => $copiedDefaultSelectBoxItem->getId()->getId(),
                'elementId' => $copiedDefaultSelectBoxItem->getElementId()->getId(),
                'name' => $copiedDefaultSelectBoxItem->getName()->jsonSerialize()
            ];
            $elementDefinitionJson['json']['defaultItem'] = $copiedDefaultItem;

            // set copied element definition
            $copiedElementDefinition = SelectBoxElementDefinition::jsonDecode($elementDefinitionJson);
            $copiedProduct->setElementDefinition(
                $copiedDefaultSelectBoxItem->getSectionId(),
                $copiedDefaultSelectBoxItem->getElementId(),
                $copiedElementDefinition
            );
        }
    }

    /**
     * @param Product $copiedProduct
     * @param array $itemMapping
     */
    private function updateProductRules(Product $copiedProduct, array $itemMapping)
    {
        /** @var Rule $rule */
        foreach ($copiedProduct->getRules() as $rule) {
            /** @var RuleCondition $condition */
            foreach ($rule->getConditions() as $condition) {
                if (!array_key_exists($condition->getValue(), $itemMapping)) {
                    continue;
                }

                /** @var SelectBoxItem $copiedSelectBoxItem */
                $copiedSelectBoxItem = $itemMapping[$condition->getValue()];
                $rule->setConditionValue(
                    $condition->getId(),
                    $copiedSelectBoxItem->getId()->getId()
                );
            }

            /** @var RuleImplication $implication */
            foreach ($rule->getImplications() as $implication) {
                if (!array_key_exists($implication->getValue(), $itemMapping)) {
                    continue;
                }

                /** @var SelectBoxItem $copiedSelectBoxItem */
                $copiedSelectBoxItem = $itemMapping[$implication->getValue()];

                $rule->setConditionValue(
                    $implication->getId(),
                    $copiedSelectBoxItem->getId()->getId()
                );
            }
        }
    }

    /**
     * @param AptoUuid $id
     * @param array $entityMapping
     * @return Product|null
     */
    private function getCopiedProduct(AptoUuid $id, array $entityMapping): ?Product
    {
        if (!array_key_exists($id->getId(), $entityMapping)) {
            return null;
        }

        $newProductId = $entityMapping[$id->getId()];
        return $this->productRepository->findById($newProductId->getId());
    }

    /**
     * @param DomainEvent $event
     * @return bool
     */
    public function isSubscribedTo(DomainEvent $event)
    {
        return (
            $event instanceof ProductCopied ||
            $event instanceof ProductSectionCopied ||
            $event instanceof ProductElementCopied
        );
    }
}
