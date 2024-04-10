<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Part;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPrices;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidOperatorException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidPropertyException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidValueException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionOperator;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\ElementUsage;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\ProductUsage;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\Quantity;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\QuantityCalculation;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\RuleCondition;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\RuleUsage;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\SectionUsage;
use Apto\Plugins\PartsList\Domain\Core\Model\Unit\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use InvalidArgumentException;

class Part extends AptoAggregate
{
    use AptoPrices;
    /**
     * @var bool
     */
    protected $active;

    /**
     * @var string
     */
    protected $partNumber;

    /**
     * @var Unit|null
     */
    protected $unit;

    /**
     * @var AptoTranslatedValue
     */
    protected $name;

    /**
     * @var AptoTranslatedValue
     */
    protected $description;

    /**
     * @var Collection
     */
    protected $productUsages;

    /**
     * @var Collection
     */
    protected $sectionUsages;

    /**
     * @var Collection
     */
    protected $elementUsages;

    /**
     * @var Collection
     */
    protected $ruleUsages;

    /**
     * @var Collection
     */
    protected $rules;

    /**
     * @var int
     */
    protected $baseQuantity;

    /**
     * @var Collection
     */
    protected $associatedProducts;

    /**
     * Part constructor.
     * @param AptoUuid $id
     * @param bool $active
     * @param AptoTranslatedValue $name
     * @throws InvalidTranslatedValueException
     */
    public function __construct(AptoUuid $id, bool $active, AptoTranslatedValue $name)
    {
        parent::__construct($id);
        $this->active = $active;
        $this->name = $name;

        $this->baseQuantity = 1;
        $this->partNumber = '';
        $this->unit = null;
        $this->description = new AptoTranslatedValue([]);
        $this->aptoPrices = new ArrayCollection();
        $this->productUsages = new ArrayCollection();
        $this->sectionUsages = new ArrayCollection();
        $this->elementUsages = new ArrayCollection();
        $this->ruleUsages = new ArrayCollection();
        $this->rules = new ArrayCollection();
        $this->associatedProducts = new ArrayCollection();
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return Part
     */
    public function setActive(bool $active): Part
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return string
     */
    public function getPartNumber(): string
    {
        return $this->partNumber;
    }

    /**
     * @param string $partNumber
     * @return Part
     */
    public function setPartNumber(string $partNumber): Part
    {
        $this->partNumber = $partNumber;
        return $this;
    }

    /**
     * @return Unit|null
     */
    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    /**
     * @param Unit|null $unit
     * @return Part
     */
    public function setUnit(?Unit $unit): Part
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getName(): AptoTranslatedValue
    {
        return $this->name;
    }

    /**
     * @param AptoTranslatedValue $name
     * @return Part
     */
    public function setName(AptoTranslatedValue $name): Part
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getDescription(): AptoTranslatedValue
    {
        return $this->description;
    }

    /**
     * @param AptoTranslatedValue $description
     * @return Part
     */
    public function setDescription(AptoTranslatedValue $description): Part
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getProductUsages(): Collection
    {
        return $this->productUsages;
    }

    /**
     * @return Collection
     */
    public function getSectionUsages(): Collection
    {
        return $this->sectionUsages;
    }

    /**
     * @return Collection
     */
    public function getElementUsages(): Collection
    {
        return $this->elementUsages;
    }

    /**
     * @param AptoUuid $id
     * @return SectionUsage
     */
    public function getSectionUsage(AptoUuid $id): SectionUsage
    {
        return $this->sectionUsages->get($id->getId());
    }

    /**
     * @param AptoUuid $id
     * @return ElementUsage
     */
    public function getElementUsage(AptoUuid $id): ElementUsage
    {
        return $this->elementUsages->get($id->getId());
    }

    /**
     * @param AptoUuid $id
     * @return ProductUsage
     */
    public function getProductUsage(AptoUuid $id): ProductUsage
    {
        return $this->productUsages->get($id->getId());
    }

    /**
     * @return Collection
     */
    public function getRuleUsages(): Collection
    {
        return $this->ruleUsages;
    }

    /**
     * @param AptoUuid $id
     * @return RuleUsage
     */
    public function getRuleUsage(AptoUuid $id): RuleUsage
    {
        return $this->ruleUsages->get($id->getId());
    }

    /**
     * @param AptoUuid $usageForUuid
     * @param Quantity $quantity
     * @return $this
     */
    public function addProductUsage(AptoUuid $usageForUuid, Quantity $quantity): Part
    {
        $productUsage = new Usage\ProductUsage($this, $this->nextUsageId(), $usageForUuid, $quantity);
        $this->productUsages->set($productUsage->getId()->getId(), $productUsage);

        return $this;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function addPartProductAssociation(Product $product): Part
    {
        /** @var $assoc PartProductAssociation */
        if ($assoc = $this->productAlreadyAssociated($product->getId())) {
            $assoc->addAssoc();
        }
        else {
            $this->associatedProducts->add(new PartProductAssociation(new AptoUuid(), $this, $product ));
        }
        return $this;
    }

    /**
     * @param AptoUuid $productId
     * @return Part
     * @throws Exception
     */
    public function removePartProductAssociation(AptoUuid $productId): Part
    {
        $assoc = $this->productAlreadyAssociated($productId);
        if (!$assoc) {
            throw new Exception('Association to remove does not exist');
        }
        /** @var $assoc PartProductAssociation */
        if ($assoc->removeAssoc()->getCount() === 0) {
            $this->associatedProducts->removeElement($assoc);
        }
        return $this;
    }

    /**
     * @param AptoUuid $usageForUuid
     * @param Quantity $quantity
     * @param AptoUuid $productId
     * @return $this
     */
    public function addSectionUsage(AptoUuid $usageForUuid, Quantity $quantity, AptoUuid $productId): Part
    {
        $sectionUsage = new Usage\SectionUsage($this, $this->nextUsageId(), $usageForUuid, $quantity, $productId);
        $this->sectionUsages->set($sectionUsage->getId()->getId(), $sectionUsage);
        return $this;
    }

    /**
     * @param AptoUuid $usageForUuid
     * @param Quantity $quantity
     * @param AptoUuid $productId
     * @return $this
     */
    public function addElementUsage(AptoUuid $usageForUuid, Quantity $quantity, AptoUuid $productId): Part
    {
        $elementUsage = new Usage\ElementUsage($this, $this->nextUsageId(), $usageForUuid, $quantity, $productId);
        $this->elementUsages->set($elementUsage->getId()->getId(), $elementUsage);
        return $this;
    }

    /**
     * @param string $name
     * @param Quantity $quantity
     * @return $this
     */
    public function addRuleUsage(string $name, Quantity $quantity): Part
    {
        $ruleUsage = new Usage\RuleUsage($this, $this->nextUsageId(), $quantity, $name);
        $this->ruleUsages->set($ruleUsage->getId()->getId(), $ruleUsage);
        return $this;
    }

    /**
     * @param AptoUuid $usageId
     * @param Quantity $quantity
     * @return Part
     */
    public function setProductUsageQuantity(AptoUuid $usageId, Quantity $quantity): Part
    {
        /** @var ProductUsage|null $productUsage */
        $productUsage = $this->productUsages->get($usageId->getId());

        if (null !== $productUsage) {
            $productUsage->setQuantity($quantity);
        }

        return $this;
    }

    /**
     * @param AptoUuid $usageId
     * @param Quantity $quantity
     * @return Part
     */
    public function setSectionUsageQuantity(AptoUuid $usageId, Quantity $quantity): Part
    {
        /** @var SectionUsage|null $sectionUsage */
        $sectionUsage = $this->sectionUsages->get($usageId->getId());

        if (null !== $sectionUsage) {
            $sectionUsage->setQuantity($quantity);
        }

        return $this;
    }

    /**
     * @param AptoUuid $usageId
     * @param Quantity $quantity
     * @return Part
     */
    public function setElementUsageQuantity(AptoUuid $usageId, Quantity $quantity): Part
    {
        /** @var ElementUsage|null $elementUsage */
        $elementUsage = $this->elementUsages->get($usageId->getId());

        if (null !== $elementUsage) {
            $elementUsage->setQuantity($quantity);
        }

        return $this;
    }

    /**
     * @param AptoUuid $usageId
     * @param Quantity $quantity
     * @return Part
     */
    public function setRuleUsageQuantity(AptoUuid $usageId, Quantity $quantity): Part
    {
        /** @var RuleUsage|null $elementUsage */
        $ruleUsage = $this->ruleUsages->get($usageId->getId());

        if (null !== $ruleUsage) {
            $ruleUsage->setQuantity($quantity);
        }

        return $this;
    }

    /**
     * @param AptoUuid $usageId
     * @param string $name
     * @return Part
     */
    public function setRuleUsageName(AptoUuid $usageId, string $name): Part
    {
        /** @var RuleUsage|null $elementUsage */
        $ruleUsage = $this->ruleUsages->get($usageId->getId());

        if (null !== $ruleUsage) {
            $ruleUsage->setName($name);
        }

        return $this;
    }

    /**
     * @param AptoUuid $usageId
     * @param bool $active
     * @return Part
     */
    public function setRuleUsageActive(AptoUuid $usageId, bool $active): Part
    {
        /** @var RuleUsage|null $elementUsage */
        $ruleUsage = $this->ruleUsages->get($usageId->getId());

        if (null !== $ruleUsage) {
            $ruleUsage->setActive($active);
        }

        return $this;
    }

    /**
     * @param AptoUuid $usageId
     * @param int $operator
     * @return Part
     */
    public function setRuleUsageConditionsOperator(AptoUuid $usageId, int $operator): Part
    {
        /** @var RuleUsage|null $elementUsage */
        $ruleUsage = $this->ruleUsages->get($usageId->getId());

        if (null !== $ruleUsage) {
            $ruleUsage->setConditionsOperator($operator);
        }

        return $this;
    }

    /**
     * @param AptoUuid $usageId
     * @param Product $product
     * @param CriterionOperator $operator
     * @param string $value
     * @param string|null $sectionId
     * @param string|null $elementId
     * @param string|null $property
     * @param AptoUuid|null $conditionId
     * @param string|null $computedValueId
     * @return $this
     * @throws InvalidUuidException
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidValueException
     */
    public function addRuleUsageCondition(
        AptoUuid          $usageId,
        Product           $product,
        CriterionOperator $operator,
        string            $value,
        string            $sectionId = null,
        string            $elementId = null,
        string            $property = null,
        AptoUuid          $conditionId = null,
        string            $computedValueId = null
    ): Part {
        /** @var RuleUsage $ruleUsage */
        $ruleUsage = $this->ruleUsages->get($usageId->getId());

        if (null === $ruleUsage) {
            return $this;
        }

        if ($conditionId === null) {
            $conditionId = $ruleUsage->nextConditionId();
        }
        $condition = new RuleCondition(
            $conditionId, $ruleUsage, $product, $operator, $value, $sectionId, $elementId, $property, $computedValueId
        );

        $ruleUsage->addCondition($condition);
        return $this;
    }

    /**
     * @param AptoUuid $usageId
     * @param AptoUuid $conditionId
     * @return Part
     * @throws Exception
     */
    public function removeRuleUsageCondition(AptoUuid $usageId, AptoUuid $conditionId): Part
    {
        /** @var RuleUsage $ruleUsage */
        $ruleUsage = $this->ruleUsages->get($usageId->getId());

        if (null === $ruleUsage) {
            return $this;
        }
        $productId = $ruleUsage->getCondition($conditionId)->getProductId();
        $ruleUsage->removeCondition($conditionId);
        $this->removePartProductAssociation($productId);
        return $this;
    }

    /**
     * @param AptoUuid $usageId
     * @param QuantityCalculation $quantityCalculation
     * @return Part
     */
    public function setElementUsageQuantityCalculation(AptoUuid $usageId, QuantityCalculation $quantityCalculation): Part
    {
        /** @var ElementUsage|null $elementUsage */
        $elementUsage = $this->elementUsages->get($usageId->getId());

        if (null !== $elementUsage) {
            $elementUsage->setQuantityCalculation($quantityCalculation);
        }

        return $this;
    }

    /**
     * @param AptoUuid $usageId
     * @return Part
     */
    public function removeProductUsage(AptoUuid $usageId): Part
    {
        if ($this->productUsages->containsKey($usageId->getId())) {
            $this->productUsages->remove($usageId->getId());
        }

        return $this;
    }

    /**
     * @param AptoUuid $usageId
     * @return Part
     */
    public function removeSectionUsage(AptoUuid $usageId): Part
    {
        if ($this->sectionUsages->containsKey($usageId->getId())) {
            $this->sectionUsages->remove($usageId->getId());
        }

        return $this;
    }

    /**
     * @param AptoUuid $usageId
     * @return Part
     */
    public function removeElementUsage(AptoUuid $usageId): Part
    {
        if ($this->elementUsages->containsKey($usageId->getId())) {
            $this->elementUsages->remove($usageId->getId());
        }

        return $this;
    }

    /**
     * @param AptoUuid $usageId
     * @return Part
     * @throws Exception
     */
    public function removeRuleUsage(AptoUuid $usageId): Part
    {
        if (!$this->ruleUsages->containsKey($usageId->getId())) {
            throw new InvalidArgumentException('No ruleUsage for given id ' . $usageId->getId() . ' found');
        }

        /** @var RuleUsage $ruleUsage */
        $ruleUsage = $this->ruleUsages->get($usageId->getId());
        /** @var RuleCondition $condition */
        foreach ($ruleUsage->getConditions() as $condition) {
            $this->removePartProductAssociation($condition->getProductId());
        }
        $this->ruleUsages->remove($usageId->getId());
        return $this;
    }

    /**
     * @return int
     */
    public function getBaseQuantity(): int
    {
        return $this->baseQuantity;
    }

    /**
     * @param int $baseQuantity
     * @return Part
     */
    public function setBaseQuantity(int $baseQuantity): Part
    {
        $this->baseQuantity = $baseQuantity;
        return $this;
    }

    /**
     * @return AptoUuid
     */
    public function nextUsageId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @param AptoUuid $productId
     * @return PartProductAssociation|bool|mixed
     */
    private function productAlreadyAssociated(AptoUuid $productId)
    {
        /** @var PartProductAssociation $associatedProduct */
        foreach ($this->associatedProducts as $associatedProduct) {
            if ($productId->getId() === $associatedProduct->getProduct()->getId()->getId()) {
                return $associatedProduct;
            }
        }
        return false;
    }
}
