<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Dql;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;

class ImmutableConfigurationDqlService extends AbstractDqlService
{
    /**
     * @param array $conditions
     * @return array
     * @throws DqlBuilderException
     */
    public function findByCustomPropertyValues(array $conditions)
    {
        // preselect query for first condition
        $firstConditionKey = key($conditions);
        if (null === $firstConditionKey) {
            $where = '';
            $parameters = [];
        } else {
            $firstConditionValue = $conditions[$firstConditionKey];
            unset($conditions[$firstConditionKey]);

            $where = 'cp.key = :key AND cp.value = :value';
            $parameters = [
                'key' => $firstConditionKey,
                'value' => $firstConditionValue
            ];
        }

        $builder = $this->getConfigurationQueryBuilder();
        $builder->setWhere($where, $parameters);
        $configurations = $builder->getResult($this->entityManager);

        // collect ids for where in query
        $configurationIds = [];
        foreach ($configurations['data'] as $configuration) {
            $configurationIds[] = $configuration['id'];
        }

        $builder = $this->getConfigurationQueryBuilder();
        $builder->setWhere('i.id.id in (:configurationIds)', ['configurationIds' => $configurationIds]);
        $result = $builder->getResult($this->entityManager);

        // search for configurations who match all conditions
        $matchedConfigurations = [];
        foreach ($result['data'] as $configuration) {
            $matchedAllConditions = true;
            foreach ($conditions as $key => $value) {
                if (false === $this->customPropertyExists($configuration['customProperties'], $key, $value)) {
                    $matchedAllConditions = false;
                }
            }

            if (true === $matchedAllConditions) {
                $matchedConfigurations[] = $configuration;
            }
        }

        // return matched configurations as result
        return [
            'numberOfRecords' => count($matchedConfigurations),
            'data' => $matchedConfigurations
        ];
    }

    /**
     * @return DqlQueryBuilder
     */
    private function getConfigurationQueryBuilder(): DqlQueryBuilder
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'i' => [
                    ['id.id', 'id'],
                    'created',
                    'state'
                ],
                'p' => [
                    ['id.id', 'id']
                ],
                'cp' => [
                    'surrogateId',
                    'key',
                    'value',
                    'translatable'
                ],
            ])->setJoins([
                'i' => [
                    ['product', 'p', 'id'],
                    ['customProperties', 'cp', 'surrogateId']
                ]
            ]);
        return $builder;
    }

    /**
     * @param array $customProperties
     * @param string $key
     * @param string $value
     * @return bool
     */
    private function customPropertyExists(array $customProperties, string $key, string $value): bool
    {
        foreach ($customProperties as $customProperty) {
            if ($customProperty['key'] === $key && $customProperty['value'] === $value) {
                return true;
            }
        }
        return false;
    }
}
