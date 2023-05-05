<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Backend\Model\UserRole\UserRole;
use Apto\Base\Application\Backend\Query\UserRole\UserRoleFinder;

class UserRoleOrmFinder extends AptoOrmFinder implements UserRoleFinder
{

    const ENTITY_CLASS = UserRole::class;
    
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass, 'r');
        $builder
            ->findById($id)
            ->setValues([
                'r' => [
                    ['id.id', 'id'],
                    ['identifier.identifier', 'identifier'],
                    'name'
                ],
                'c' => [
                    ['id.id', 'id'],
                    ['identifier.identifier', 'identifier'],
                    'name'
                ]
            ])
            ->setJoins([
                'r' => [
                    ['children', 'c', 'id']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $searchString
     * @return array
     */
    public function FindUserRoles(string $searchString = ''): array
    {
        $builder = new DqlPaginatorBuilder($this->entityClass, 'r');
        $builder
            ->setValues([
                'r' => [
                    ['id.id', 'id'],
                    ['identifier.identifier', 'identifier'],
                    'name'
                ],
                'c' => [
                    ['id.id', 'id'],
                    ['identifier.identifier', 'identifier'],
                    'name'
                ]
            ])
            ->setJoins([
                'r' => [
                    ['children', 'c', 'id']
                ]
            ])
            ->setSearch([
                'r' => [
                    'identifier.identifier',
                    'name'
                ]
            ], $searchString);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param array $directUserRoles
     * @return array
     */
    public function findInheritedUserRoles(array $directUserRoles): array
    {
        $hierarchyTree = $this->getRoleHierarchyTree();
        $userRoles = [];

        foreach ($directUserRoles as $directUserRole) {
            if (!array_key_exists('identifier', $directUserRole)) {
                continue;
            }
            $userRoles[$directUserRole['identifier']] = $directUserRole['identifier'];
            $userRoles = $this->getInheritedRoles($directUserRole['identifier'], $hierarchyTree, $userRoles);
        }

        return array_values($userRoles);
    }

    /**
     * @param string $userRole
     * @param array $hierarchyTree
     * @param array $inheritedRoles
     * @return array
     */
    private function getInheritedRoles(string $userRole, array $hierarchyTree, array $inheritedRoles): array
    {
        foreach ($hierarchyTree as $key => $values) {
            if ($userRole === $key) {
                $inheritedRoles[$key] = $key;
                foreach ($values as $value) {
                    $inheritedRoles[$value] = $value;
                    $inheritedRoles = $this->getInheritedRoles($value, $hierarchyTree, $inheritedRoles);
                }
            }
        }

        return $inheritedRoles;
    }

    /**
     * @return array
     */
    private function getRoleHierarchyTree(): array
    {
        $hierarchyTree = [];
        $dql = 'SELECT partial r.{surrogateId,identifier.identifier}, partial c.{surrogateId,identifier.identifier} FROM ' . $this->entityClass . ' r LEFT JOIN r.children c';
        $query = $this->entityManager->createQuery($dql);
        $roles = $query->getScalarResult();

        foreach ($roles as $row) {
            $identifier = $row['r_identifier.identifier'];
            $child = $row['c_identifier.identifier'];
            if ('' != $child) {
                if (key_exists($identifier, $hierarchyTree)) {
                    if (!in_array($child, $hierarchyTree[$identifier])) {
                        $hierarchyTree[$identifier][] = $child;
                    }
                } else {
                    $hierarchyTree[$identifier] = [$child];
                }
            }
        }

        return $hierarchyTree;
    }
}