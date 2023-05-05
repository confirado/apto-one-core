<?php
namespace Apto\Catalog\Domain\Core\Model\Group;

use Apto\Base\Domain\Core\Model\AptoRepository;
use Apto\Catalog\Domain\Core\Model\Product\IdentifierNullable;

interface GroupRepository extends AptoRepository
{
    /**
     * @param Group $model
     */
    public function update(Group $model);

    /**
     * @param Group $model
     */
    public function add(Group $model);

    /**
     * @param Group $model
     */
    public function remove(Group $model);

    /**
     * @param $id
     * @return Group|null
     */
    public function findById($id);

    /**
     * @param IdentifierNullable $identifier
     * @return Group|null
     */
    public function findByIdentifier(IdentifierNullable $identifier);
}