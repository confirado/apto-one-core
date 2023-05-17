<?php

namespace Apto\Plugins\ImageUpload\Domain\Core\Model\Canvas;

use Apto\Base\Domain\Core\Model\AptoRepository;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;

interface CanvasRepository extends AptoRepository
{
    /**
     * @param Canvas $model
     */
    public function update(Canvas $model);

    /**
     * @param Canvas $model
     */
    public function add(Canvas $model);

    /**
     * @param Canvas $model
     */
    public function remove(Canvas $model);

    /**
     * @param AptoUuid $id
     * @return Canvas|null
     */
    public function findById(AptoUuid $id): ?Canvas;

    /**
     * @param Identifier $identifier
     * @return Canvas|null
     */
    public function findByIdentifier(Identifier $identifier): ?Canvas;

    /**
     * @param Identifier $identifier
     * @return AptoUuid|null
     */
    public function findIdByIdentifier(Identifier $identifier): ?AptoUuid;
}
