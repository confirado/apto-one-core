<?php

namespace Apto\Plugins\ImportExport\Application\Backend\Commands\Import\Rule;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoTranslatedValueItem;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\DefaultCriterion;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionOperator;
use Exception;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;
use Apto\Catalog\Domain\Core\Model\Product\Rule\Rule;

class RuleCommandHandler extends AbstractCommandHandler
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * RuleCommandHandler constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param ImportRuleDataType $command
     * @throws Exception
     */
    public function handleImportRuleDataType(ImportRuleDataType $command)
    {
        $fields = $command->getFields();
        $locale = $command->getLocale();
        $product = $this->productRepository->findByIdentifier(new Identifier($fields['product-identifier']));
        if (null === $product) {
            return;
        }

        $ruleIds = $product->getRuleIdsByName($fields['name']);
        if (count($ruleIds) < 1) {
            $product->addRule($fields['name']);
            $ruleIds = $product->getRuleIdsByName($fields['name']);
        }

        $ruleId = new AptoUuid($ruleIds[0]);
        $product->setRuleConditionsOperator($ruleId, strtoupper($fields['conditions-operator']) === 'AND' ? Rule::OPERATOR_AND : Rule::OPERATOR_OR);
        $product->setRuleImplicationsOperator($ruleId, strtoupper($fields['implications-operator']) === 'AND' ? Rule::OPERATOR_AND : Rule::OPERATOR_OR);

        if (array_key_exists('active', $fields)) {
            $product->setRuleActive($ruleId, $this->parseBoolValue($fields['active']));
        } else {
            $product->setRuleActive($ruleId, false);
        }

        if (array_key_exists('soft-rule', $fields)) {
            $product->setSoftRule($ruleId, $this->parseBoolValue($fields['soft-rule']));
        } else {
            $product->setSoftRule($ruleId, false);
        }

        if (array_key_exists('error-message', $fields)) {
            $product->setRuleErrorMessage(
                $ruleId,
                AptoTranslatedValue::addTranslation(
                    $product->getRuleErrorMessage($ruleId) ?? new AptoTranslatedValue([]),
                    new AptoTranslatedValueItem(new AptoLocale($locale), $fields['error-message'])
                )
            );
        }

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param ImportRuleConditionDataType $command
     * @throws Exception
     */
    public function handleImportRuleConditionDataType(ImportRuleConditionDataType $command)
    {
        $fields = $command->getFields();
        $product = $this->productRepository->findByIdentifier(new Identifier($fields['product-identifier']));
        if (null === $product) {
            return;
        }

        $ruleIds = $product->getRuleIdsByName($fields['name']);
        $ruleId = null;
        if (count($ruleIds) < 1) {
            $product->addRule($fields['name']);
            $ruleIds = $product->getRuleIdsByName($fields['name']);
            $ruleId = new AptoUuid($ruleIds[0]);
            $product->setRuleActive($ruleId, true);
        } else {
            $ruleId = new AptoUuid($ruleIds[0]);
        }

        $sectionId = $this->getCriterionSectionId($product, $fields['section-identifier']);
        $elementId = $this->getCriterionElementId($product, $sectionId, $fields['element-identifier']);
        $operator = $this->getCriterionOperator($fields['operator']);

        $property = null;
        if (array_key_exists('property', $fields)) {
            $property = $this->getCriterionProperty($fields['property']);
        }

        $value = '';
        if (array_key_exists('value', $fields)) {
            $value = $this->getCriterionValue($fields['value']);
        }

        $product->addRuleCondition(
            $ruleId,
            $operator,
            DefaultCriterion::TYPE,
            $sectionId,
            $elementId,
            $property,
            null,
            $value
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param ImportRuleImplicationDataType $command
     * @throws Exception
     */
    public function handleImportRuleImplicationDataType(ImportRuleImplicationDataType $command)
    {
        $fields = $command->getFields();
        $product = $this->productRepository->findByIdentifier(new Identifier($fields['product-identifier']));
        if (null === $product) {
            return;
        }

        $ruleIds = $product->getRuleIdsByName($fields['name']);
        $ruleId = null;
        if (count($ruleIds) < 1) {
            $product->addRule($fields['name']);
            $ruleIds = $product->getRuleIdsByName($fields['name']);
            $ruleId = new AptoUuid($ruleIds[0]);
            $product->setRuleActive($ruleId, true);
        } else {
            $ruleId = new AptoUuid($ruleIds[0]);
        }

        $sectionId = $this->getCriterionSectionId($product, $fields['section-identifier']);
        $elementId = $this->getCriterionElementId($product, $sectionId, $fields['element-identifier']);
        $operator = $this->getCriterionOperator($fields['operator']);

        $property = null;
        if (array_key_exists('property', $fields)) {
            $property = $this->getCriterionProperty($fields['property']);
        }

        $value = '';
        if (array_key_exists('value', $fields)) {
            $value = $this->getCriterionValue($fields['value']);
        }

        $product->addRuleImplication(
            $ruleId,
            $operator,
            DefaultCriterion::TYPE,
            $sectionId,
            $elementId,
            $property,
            null,
            $value
        );

        $this->productRepository->update($product);
        $product->publishEvents();
    }

    /**
     * @param PreImportRuleConditionDataType $command
     * @throws Exception
     */
    public function handlePreImportRuleConditionDataType(PreImportRuleConditionDataType $command)
    {
        $this->clearAllCriterions('condition', $command->getFields());
    }

    /**
     * @param PreImportRuleImplicationDataType $command
     * @throws Exception
     */
    public function handlePreImportRuleImplicationDataType(PreImportRuleImplicationDataType $command)
    {
        $this->clearAllCriterions('implication', $command->getFields());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield ImportRuleDataType::class => [
            'method' => 'handleImportRuleDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];

        yield ImportRuleConditionDataType::class => [
            'method' => 'handleImportRuleConditionDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];

        yield ImportRuleImplicationDataType::class => [
            'method' => 'handleImportRuleImplicationDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];

        yield PreImportRuleConditionDataType::class => [
            'method' => 'handlePreImportRuleConditionDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];

        yield PreImportRuleImplicationDataType::class => [
            'method' => 'handlePreImportRuleImplicationDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];
    }

    /**
     * @param array $fields
     * @return array
     */
    private function getProductIdentifiers(array $fields): array
    {
        $products = [];

        foreach ($fields as $lineFields) {
            $product = $lineFields['product-identifier'];
            if (!in_array($product, $products)) {
                $products[] = $product;
            }
        }

        return $products;
    }

    /**
     * @param string $productIdentifier
     * @param array $fields
     * @return array
     */
    private function getRuleNamesByProductIdentifier(string $productIdentifier, array $fields): array
    {
        $ruleNames = [];

        foreach ($fields as $lineFields) {
            if ($lineFields['product-identifier'] !== $productIdentifier) {
                continue;
            }

            $ruleName = $lineFields['name'];
            if (!in_array($ruleName, $ruleNames)) {
                $ruleNames[] = $ruleName;
            }
        }

        return $ruleNames;
    }

    /**
     * @param string $type
     * @param array $fields
     * @throws Exception
     */
    private function clearAllCriterions(string $type, array $fields)
    {
        $productIdentifiers = $this->getProductIdentifiers($fields);
        foreach ($productIdentifiers as $productIdentifier) {
            $product = $this->productRepository->findByIdentifier(new Identifier($productIdentifier));
            if (null === $product) {
                continue;
            }

            $ruleNamesForProduct = $this->getRuleNamesByProductIdentifier($productIdentifier, $fields);
            /** @var Rule $rule */
            foreach ($product->getRules() as $rule) {
                if (!in_array($rule->getName(), $ruleNamesForProduct)) {
                    continue;
                }

                switch ($type) {
                    case 'condition': {
                        $rule->removeAllConditions();
                        break;
                    }
                    case 'implication': {
                        $rule->removeAllImplications();
                        break;
                    }
                }
            }

            $this->productRepository->update($product);
            $product->publishEvents();
        }
    }

    /**
     * @param Product $product
     * @param string $identifier
     * @return AptoUuid
     * @throws Exception
     */
    private function getCriterionSectionId(Product $product, string $identifier): AptoUuid
    {
        $identifier = trim($identifier);
        $sectionId = $product->getSectionIdByIdentifier(new Identifier($identifier));

        if (null !== $sectionId) {
            return $sectionId;
        }

        throw new \InvalidArgumentException('Section with identifier "' . $identifier . '" not found.');
    }

    /**
     * @param Product $product
     * @param AptoUuid $sectionId
     * @param string $identifier
     * @return AptoUuid|null
     * @throws Exception
     */
    private function getCriterionElementId(Product $product, AptoUuid $sectionId, string $identifier): ?AptoUuid
    {
        $identifier = trim($identifier);
        if (strlen($identifier) < 1) {
            return null;
        }

        $elementId = $product->getElementIdByIdentifier($sectionId, new Identifier($identifier));
        if (null !== $elementId) {
            return $elementId;
        }

        throw new \InvalidArgumentException('Element with identifier "' . $identifier . '" not found.');
    }

    /**
     * @param string $property
     * @return string|null
     */
    private function getCriterionProperty(string $property): ?string
    {
        $property = trim($property);
        if (strlen($property) > 0) {
            return $property;
        }
        return null;
    }

    /**
     * @param string $operator
     * gleich:        eq
     * kleiner:       lt
     * größer:        gt
     * kleinergleich: le
     * größergleich:  ge
     * nichtgleich:   ne
     * active:        active
     * nichtactive:   inactive
     * @return CriterionOperator
     */
    private function getCriterionOperator(string $operator): CriterionOperator
    {
        switch ($operator) {
            case 'eq': {
                return new CriterionOperator(CriterionOperator::EQUAL);
            }
            case 'lt': {
                return new CriterionOperator(CriterionOperator::LOWER);
            }
            case 'gt': {
                return new CriterionOperator(CriterionOperator::GREATER);
            }
            case 'le': {
                return new CriterionOperator(CriterionOperator::LOWER_OR_EQUAL);
            }
            case 'ge': {
                return new CriterionOperator(CriterionOperator::GREATER_OR_EQUAL);
            }
            case 'ne': {
                return new CriterionOperator(CriterionOperator::NOT_EQUAL);
            }
            case 'active': {
                return new CriterionOperator(CriterionOperator::ACTIVE);
            }
            case 'inactive': {
                return new CriterionOperator(CriterionOperator::NOT_ACTIVE);
            }
        }

        throw new \InvalidArgumentException('No match found for given operator "' . $operator .  '".');
    }

    /**
     * @param string $value
     * @return string
     */
    private function getCriterionValue(string $value): string
    {
        $value = trim($value);
        if (strlen($value) > 0) {
            return $value;
        }
        return '';
    }

    /**
     * @param string $value
     * @return bool
     */
    protected function parseBoolValue(string $value): bool
    {
        $value = strtolower(trim($value));
        if ($value == 1 || $value == 'x' || $value == 'ja' || $value == 'wahr' || $value == 'true' || $value == 'active') {
            return true;
        }
        return false;
    }
}
