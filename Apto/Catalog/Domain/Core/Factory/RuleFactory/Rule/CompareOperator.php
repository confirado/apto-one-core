<?php

namespace Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Exception\CompareOperatorInvalidValueException;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use InvalidArgumentException;

class CompareOperator
{
    /**
     * Valid values
     */
    const NOT_ACTIVE = 0;
    const ACTIVE = 1;
    const LOWER = 2;
    const LOWER_OR_EQUAL = 3;
    const EQUAL = 4;
    const NOT_EQUAL = 5;
    const GREATER_OR_EQUAL = 6;
    const GREATER = 7;
    const CONTAINS = 8;
    const NOT_CONTAINS = 9;

    /**
     * Array of all possible operators
     * @var array
     */
    protected static $validOperators =[
        self::NOT_ACTIVE => 'is not active',
        self::ACTIVE => 'is active',
        self::LOWER => '<',
        self::LOWER_OR_EQUAL => '<=',
        self::EQUAL => '==',
        self::NOT_EQUAL => '!=',
        self::GREATER_OR_EQUAL => '>=',
        self::GREATER => '>',
        self::CONTAINS => 'contains',
        self::NOT_CONTAINS => 'does not contain'
    ];

    /**
     * @var int
     */
    protected $operator;

    /**
     * @param int $operator
     */
    public function __construct(int $operator)
    {
        if (!array_key_exists($operator, self::$validOperators)) {
            throw new CompareOperatorInvalidValueException('The given value \'' . $operator . '\' is not a valid CompareOperator.');
        }

        $this->operator = $operator;
    }

    /**
     * Get this operator value
     * @return int
     */
    public function getOperator(): int
    {
        return $this->operator;
    }


    /**
     * Compares two values using the current operator
     * @param mixed $a
     * @param mixed $b
     * @return bool
     */
    public function compare($a, $b): bool
    {
        switch($this->operator) {

            case self::NOT_ACTIVE:
                return null === $a;

            case self::ACTIVE:
                return null !== $a;

            case self::LOWER:
                return $a < $b;

            case self::LOWER_OR_EQUAL:
                return $a <= $b;

            case self::EQUAL:
                return $a == $b;

            case self::NOT_EQUAL:
                return $a != $b;

            case self::GREATER_OR_EQUAL:
                return $a >= $b;

            case self::GREATER:
                return $a > $b;

            case self::CONTAINS:
                return strpos(!is_string($a) ? serialize($a) : $a, $b) !== false;
                // @TODO, might use the following implementation?
                //return in_array($b, array_map('trim', explode(',', $a)));

            case self::NOT_CONTAINS:
                return strpos(!is_string($a) ? serialize($a) : $a, $b) === false;
                // @TODO, might use the following implementation?
                //return !in_array($b, array_map('trim', explode(',', $a)));

            // something went wrong, operator should be valid at this point
            default: {
                throw new InvalidArgumentException(sprintf(
                    'The given value \'%s\' is not a valid CompareOperator.',
                     $this->operator
                ));
            }
        }
    }

    /**
     * @param ConfigurableProduct $product
     * @param State               $state
     * @param AptoUuid            $sectionId
     * @param AptoUuid|null       $elementId
     * @param string|null         $property
     * @param string|null         $value
     * @param int                 $repetition
     *
     * @return State
     */
    public function fulfill(ConfigurableProduct $product, State $state, AptoUuid $sectionId, ?AptoUuid $elementId, ?string $property, ?string $value, int $repetition = 0): State
    {
        switch($this->operator) {

            // NOT ACTIVE
            case self::NOT_ACTIVE: {
                if (null === $elementId) {
                    $state->removeSection($sectionId, $repetition);
                } else {
                    $state->removeElement($sectionId, $elementId, $repetition);
                }
                break;
            }

            // ACTIVE
            case self::ACTIVE: {
                $state->setValue($sectionId, $elementId, $repetition);
                break;
            }

            // LOWER, LOWER_OR_EQUAL, EQUAL or GREATER_OR_EQUAL
            case self::LOWER:
            case self::LOWER_OR_EQUAL:
            case self::EQUAL:
            case self::NOT_EQUAL:
            case self::GREATER_OR_EQUAL:
            case self::GREATER: {
                $valueCollection = $product->getElementValueCollection($sectionId, $elementId, $property);
                if (null === $valueCollection) {
                    throw new InvalidArgumentException(sprintf(
                        'The given property \'%s\' in element \'%s\' and section \'%s\' does not have any valid values.',
                        $property,
                        $elementId->getId(),
                        $sectionId->getId()
                    ));
                }
                switch ($this->operator) {

                    // LOWER
                    case self::LOWER: {
                        $guess = $valueCollection->getLowerValue($value);
                        break;
                    }

                    // LOWER_OR_EQUAL, EQUAL or GREATER_OR_EQUAL
                    case self::LOWER_OR_EQUAL:
                    case self::EQUAL:
                    case self::GREATER_OR_EQUAL: {
                        $guess = $valueCollection->getEqualValue($value);
                        break;
                    }

                    // GREATER
                    case self::GREATER: {
                        $guess = $valueCollection->getGreaterValue($value);
                        break;
                    }

                    // NOT_EQUAL
                    case self::NOT_EQUAL: {
                        $guess = $valueCollection->getNotEqualValue($value);
                        break;
                    }

                    default:
                        $guess = null;
                }
                if (null !== $guess) {
                    $state->setValue($sectionId, $elementId, $property, $guess, $repetition);
                }
                break;
            }

            // CONTAINS or NOT_CONTAINS
            case self::CONTAINS:
            case self::NOT_CONTAINS: {
                throw new InvalidArgumentException(sprintf(
                    'The given CompareOperator \'%s\' is not implemented for rule repair, yet.',
                    $this->operator
                ));
            }

            // something went wrong, operator should be valid at this point
            default: {
                throw new InvalidArgumentException(sprintf(
                    'The given value \'%s\' is not a valid CompareOperator.',
                    $this->operator
                ));
            }
        }

        return $state;
    }

    /**
     * Return a human-readable string representation
     * @param State $state
     * @return string
     */
    public function explain(State $state): string
    {
        return self::$validOperators[$this->operator] ?? '';
    }

}
