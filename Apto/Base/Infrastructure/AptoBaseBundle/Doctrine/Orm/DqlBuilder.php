<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;

abstract class DqlBuilder
{

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var string
     */
    protected $root;

    /**
     * @var array|null
     */
    protected $values;

    /**
     * @var array
     */
    private $valueAliasMap;

    /**
     * @var string
     */
    protected $idName;

    /**
     * @var string
     */
    protected $searchValue;

    /**
     * @var array
     */
    protected $searchFields;

    /**
     * @var string
     */
    protected $where;

    /**
     * @var array
     */
    protected $joins;

    /**
     * @var string
     */
    protected $joinType;

    /**
     * @var array
     */
    protected $orderBy;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var array
     */
    private $postProcess = [];

    /**
     * @var array
     */
    private $postProcessAliasMap;

    /**
     * DqlPaginatorBuilder constructor.
     * @param string $entityClass
     * @param string|null $root
     * @param string|null $idName
     */
    public function __construct(string $entityClass, string $root = null, string $idName = null)
    {
        $this->entityClass = $entityClass;
        $this->root = is_null($root) ? $this->getDefaultRoot() : $root;
        $this->idName = is_null($idName) ? 'surrogateId' : $idName;
        $this->orderBy = [];
    }

    /**
     * @param array $values
     * @return DqlBuilder
     */
    public function setValues(array $values): DqlBuilder
    {
        // add id (e.g. surrogateId) to select fields, if not present
        if (key_exists($this->root, $values)) {
            $found = false;
            foreach ($values[$this->root] as $key => $value) {
                if (is_array($value)) {
                    if ($value[0] == $this->idName || $value[1] == $this->idName)
                    {
                        $found = true;
                        break;
                    }
                } else if ($value == $this->idName) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $values[$this->root][] = $this->idName;
            }
        }

        $this->values = $values;
        return $this;
    }

    /**
     * @param array $fields
     * @param string $value
     * @return DqlBuilder
     */
    public function setSearch(array $fields, string $value): DqlBuilder
    {
        $this->searchFields = $fields;
        $this->searchValue = $value;
        return $this;
    }

    /**
     * @param string $where
     * @param array $parameters
     * @return DqlBuilder
     */
    public function setWhere(string $where, array $parameters = []): DqlBuilder
    {
        // append with AND, if where statement already exists
        if ('' != $this->where) {
            $this->where .= ' AND ';
        }

        $this->where .= $where;
        $this->parameters = array_merge($this->parameters, $parameters);
        return $this;
    }

    /**
     * @param array $joins
     * @param string $joinType
     * @return DqlBuilder
     */
    public function setJoins(array $joins, string $joinType = 'LEFT'): DqlBuilder
    {
        // split join array and extract root
        $this->joins = $joins;

        // sanitize join type
        $joinType = strtoupper($joinType);
        switch ($joinType) {
            case 'LEFT':
            case 'RIGHT':
                break;

            case 'INNER':
                $joinType = '';
                break;

            default:
                $joinType = 'LEFT';
        }
        $this->joinType = $joinType;

        return $this;
    }

    /**
     * @param array $orderBy
     * @return DqlBuilder
     */
    public function setOrderBy(array $orderBy): DqlBuilder
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * @param array $postProcess
     * @return DqlBuilder
     */
    public function setPostProcess(array $postProcess): DqlBuilder
    {
        $this->postProcess = $postProcess;
        return $this;
    }

    /**
     * @param EntityManagerInterface $em
     * @param bool $debug
     * @param bool $scalar
     * @return array
     * @throws DqlBuilderException
     */
    public function getResult(EntityManagerInterface $em, bool $debug = false, bool $scalar = false): array
    {
        $dql = $this->getDqlRecords();
        $query = $em->createQuery($dql);
        if(!empty($this->parameters)) {
            $query->setParameters($this->parameters);
        }
        $results = $query->getScalarResult();

        if ($scalar === false) {
            $results = $this->convertToNestedResults($results);
            $result = [
                'numberOfRecords' => count($results),
                'data' => $results
            ];
        } else {
            $result = [
                'numberOfRecords' => count($results),
                'data' => $results
            ];
        }


        if ($debug) {
            $result['sql'] = $query->getSQL();
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getEmptyResult()
    {
        return [
            'numberOfRecords' => 0,
            'data' => []
        ];
    }

    /**
     * @param array $result
     * @return array
     */
    protected function convertToNestedResults(array &$result): array
    {
        // optimize lookup tables
        $this->valueAliasMap = [];
        $this->postProcessAliasMap = [];
        foreach ($this->values as $root => $values) {
            $map = [];
            foreach ($values as $value) {
                $fieldName = is_array($value) ?  $value[1] : $value;
                $map[$fieldName] = $this->getAlias($fieldName, $root);
                if (key_exists($root, $this->postProcess) && key_exists($fieldName, $this->postProcess[$root])) {
                    $this->postProcessAliasMap[$map[$fieldName]] = &$this->postProcess[$root][$fieldName];
                }
            }
            $this->valueAliasMap[$root] = $map;
        }

        // build merged array
        $idName = $this->idName;
        $idLookup = [];
        $merged = [];
        foreach ($result as $row) {
             // populate merged result
            $this->recursiveNestedResults($row, $merged, $idLookup, $row[$this->getAlias($idName, $this->root)], $this->root);
        }

        return $merged;
    }

    /**
     * Using $lookup for mapping surrogateIds (or other ids) to numeric array positions
     * @param array $row
     * @param array $parent
     * @param array $lookup
     * @param $id
     * @param string $root
     */
    private function recursiveNestedResults(array &$row, array &$parent, array &$lookup, $id, string $root)
    {
        // populate properties of current root
        if (!key_exists($id, $lookup)) {
            $nId = count($parent);
            $parent[$nId] = [];
            $lookup[$id] = [
                'nId' => $nId,
                'children' => []
            ];
            foreach ($this->valueAliasMap[$root] as $fieldName => $aliasFieldName) {
                if (key_exists($aliasFieldName, $this->postProcessAliasMap)) {
                    $f = $this->postProcessAliasMap[$aliasFieldName];
                    $parent[$nId][$fieldName] = $f($row[$aliasFieldName], $row, $this->valueAliasMap[$root]);
                } else {
                    $parent[$nId][$fieldName] = $row[$aliasFieldName];
                }
            }
        } else {
            $nId = $lookup[$id]['nId'];
        }

        // populate joins for this root
        if (null != $this->joins && key_exists($root, $this->joins)) {
            foreach ($this->joins[$root] as $join) {
                $joinField = $join[0];
                $joinRoot = $join[1];
                $joinId = $join[2];
                $map = $this->valueAliasMap[$joinRoot];
                // property hasn't got an array, create one
                if (!key_exists($joinField, $parent[$nId])) {
                    $parent[$nId][$joinField] = [];
                    $lookup[$id]['children'][$joinField] = [];
                }
                // if item is empty, stop at this level
                $joinId = $row[$map[$joinId]];
                if (null !== $joinId) {
                    $this->recursiveNestedResults($row, $parent[$nId][$joinField], $lookup[$id]['children'][$joinField], $joinId, $joinRoot);
                }
            }
        }
    }

    /**
     * @return string
     * @throws DqlBuilderException
     */
    protected function getDqlRecords(): string
    {
        $select = 'SELECT ' . $this->getDqlSelectValues();
        $from = ' FROM ' . $this->entityClass . ' ' . $this->root;
        $join = $this->getDqlJoins();
        $where = $this->getDqlWhere();

        $order = '';
        if ($this->orderBy) {
            $order = ' ORDER BY ' . $this->getDqlOrderByValues();
        }

        return $select . $from . $join . $where . $order;
    }

    /**
     * @return string
     * @throws DqlBuilderException
     */
    protected function getDqlSelectValues(): string
    {
        $values = [];

        $orderBys = [];
        foreach ($this->orderBy as $orderBy) {
            $orderBy = ((array)$orderBy)[0];
            list($orderByRoot, $orderByValue) = explode('.', $orderBy, 2);
            $orderBys[$orderBy] = [
                'root' => $orderByRoot,
                'value' => $orderByValue
            ];
        }

        if (!is_null($this->values)) {
            // use given values
            foreach ($this->values as $root => $fields) {
                foreach ($fields as $field) {
                    $value = (array)$field;
                    $values[] =  $root . '.' . $value[0] . ' ' . $this->getAlias($value[count($value) > 1 ? 1 : 0], $root);

                    if (array_key_exists($root . '.' . $value[0], $orderBys)) {
                        unset($orderBys[$root . '.' . $value[0]]);
                    }
                    if (count($value) > 1 && array_key_exists($root . '.' . $value[1], $orderBys)) {
                        unset($orderBys[$root . '.' . $value[1]]);
                    }
                }
            }
        } else {
            throw new DqlBuilderException('Cannot build dql query without any SELECT values.');
        }

        foreach ($orderBys as $key => $value) {
            $values[] =  $key . ' ' . $this->getAlias($value['value'], $value['root']);
        }

        return implode(', ', $values);
    }

    /**
     * @return string
     */
    protected function getDqlJoins(): string
    {
        if (null === $this->joins || 0 == count($this->joins)) {
            return '';
        }
        $values = [];
        foreach ($this->joins as $root => $joins) {
            foreach ($joins as $join) {
                $values[] = $root . '.' . $join[0] . ' ' . $join[1] . (isset($join[3]) ? ' WITH ' . $join[3] : '');
            }
        }

        $type = ' ' . trim($this->joinType . ' JOIN') . ' ';

        return $type . implode($type, $values);
    }

    /**
     * @return string
     */
    protected function getDqlWhere()
    {
        $where = '';
        if ($this->where) {
            $where .= ' WHERE (' . $this->where . ')';
        }
        if ($this->searchFields && $this->searchValue) {
            $where .= ($where ? ' AND ' : ' WHERE ') . '(' . $this->getDqlSearchValues() . ')';
        }

        return $where;
    }

    /**
     * @return string
     */
    protected function getDqlOrderByValues(): string
    {
        $values = [];

        // use given values
        foreach ($this->orderBy as $value) {
            $value = (array)$value;
            $values[] =  $value[0] . (count($value) > 1 ? ' ' . $value[1] : ' ASC');
        }

        return implode(', ', $values);
    }

    /**
     * @return string
     */
    protected function getDqlSearchValues(): string
    {
        $matches = [];

        foreach ($this->searchFields as $root => $fields) {
            foreach ($fields as $field) {
                $parameterField = $this->getParameterId($field, $root);
                $matches[] = $root . '.' . $field . ' LIKE :' . $parameterField;
                $this->parameters[$parameterField] = '%' . $this->searchValue . '%';
            }
        }

        return implode(' OR ', $matches);
    }

    /**
     * @param string $value
     * @param string $root
     * @return string
     */
    protected function getAlias(string $value, string $root): string
    {
        return '_' . $root . '_' . str_replace('.', '__', $value);
    }

    /**
     * @param string $field
     * @param string $root
     * @return string
     */
    protected function getParameterId(string $field, string $root): string
    {
        return str_replace('.', '_', $root . '_' . $field);
    }

    /**
     * @return string
     */
    private function getDefaultRoot(): string
    {
        $parts = explode('\\', $this->entityClass);
        return strtolower(substr(end($parts), 0, 1));
    }

    /**
     * decode a given value to a boolean value
     * @param $value
     * @return bool
     */
    public static function decodeBool($value): bool
    {
        return $value ? true : false;
    }

    /**
     * decode a given value to a integer value
     * @param $value
     * @return int
     */
    public static function decodeInteger($value): int
    {
        return intval($value);
    }

    /**
     * decode a given value to a integer value
     * @param $value
     * @return integer|null
     */
    public static function decodeIntegerOrNull($value)
    {
        return $value === null ? null : intval($value);
    }

    /**
     * decode a given value to a double value
     * @param $value
     * @return float
     */
    public static function decodeFloat($value): float
    {
        return floatval($value);
    }

    /**
     * decode a given value to a double value
     * @param $value
     * @return float|null
     */
    public static function decodeFloatOrNull($value)
    {
        return $value === null ? null : floatval($value);
    }

    /**
     * decode a given value to a double value
     * @param $value
     * @return string
     */
    public static function castString($value): string
    {
        return (string) $value;
    }

    /**
     * decode a given value from json to an assoc array
     * @param $value
     * @return mixed
     */
    public static function decodeJson($value)
    {
        return json_decode($value, true);
    }

    /**
     * decode a given value from decimal string by removing trailing zeros
     * @param $value
     * @return mixed
     */
    public static function decodeDecimal($value)
    {
        return rtrim(rtrim($value, '0'), '.');
    }

    /**
     * @param $value
     * @param $row
     * @param $fieldMap
     * @return string|array|null
     */
    public static function decodeCustomPropertyValue($value, $row, $fieldMap)
    {
        if (!$row[$fieldMap['translatable']]) {
            return $value;
        }

        return json_decode($value, true);
    }
}