<?php

namespace Apto\Catalog\Domain\Core\Factory\RuleFactory;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\CompareOperator;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\ComputedProductValueCriterion;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Condition;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Criterion;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\DefaultCriterion;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Implication;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\LinkOperator;

class RuleFactory
{
    /**
     * Create a new Rule value object from raw array
     * @param array $rawRule
     * @return Rule
     * @throws InvalidUuidException
     */
    public static function fromArray(array $rawRule): Rule
    {
        $conditionCriteria = self::criteriaFromArray($rawRule['conditions']);
        $implicationCriteria = self::criteriaFromArray($rawRule['implications']);

        return new Rule(
            new AptoUuid($rawRule['id']),
            $rawRule['active'],
            $rawRule['softRule'],
            $rawRule['name'],
            AptoTranslatedValue::fromArray($rawRule['errorMessage'] ?: []),
            new Condition(
                new LinkOperator($rawRule['conditionsOperator']),
                $conditionCriteria
            ),
            new Implication(
                new LinkOperator($rawRule['implicationsOperator']),
                $implicationCriteria
            )
        );
    }

    /**
     * Create a new list of criteria from raw array
     * @param array $rawCriteria
     * @return Criterion[]
     * @throws InvalidUuidException
     */
    protected static function criteriaFromArray(array $rawCriteria): array
    {
        $criteria = [];

        foreach ($rawCriteria as $rawCriterion) {
            $criteria[] = self::criterionFromArray($rawCriterion);
        }

        return $criteria;
    }

    /**
     * @param array $rawCriterion
     * @return Criterion
     * @throws InvalidUuidException
     */
    protected static function criterionFromArray(array $rawCriterion): Criterion
    {
        switch ($rawCriterion['type']) {

            // computed product value criterion
            case ComputedProductValueCriterion::TYPE: {
                $criterion = new ComputedProductValueCriterion(
                    $rawCriterion['active'] ?? true, // @TODO may be deprecated after implementation of active flag on criteria entity
                    new CompareOperator($rawCriterion['operator']),
                    $rawCriterion['value'] ?? null,
                    $rawCriterion['computedProductValue'][0]['id']
                );
                break;
            }

            // default criterion
            case DefaultCriterion::TYPE:
            default: {
                $criterion = new DefaultCriterion(
                    $rawCriterion['active'] ?? true, // @TODO may be deprecated after implementation of active flag on criteria entity
                    new CompareOperator($rawCriterion['operator']),
                    $rawCriterion['value'] ?? null,
                    new AptoUuid($rawCriterion['sectionId']),
                    $rawCriterion['elementId'] ? new AptoUuid($rawCriterion['elementId']) : null,
                    $rawCriterion['property'] ?? null
                );
            }

        }

        return $criterion;
    }

}