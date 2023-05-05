<?php
namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface ProductRepository extends AptoRepository
{
    /**
     * @ todo it looks like we don't need an update method anymore, doctrine seems to handle updates automatically because every command is coupled in a transaction, need to to some accurate research here if we can remove this method in every repository
     * @param Product $model
     */
    public function update(Product $model);

    /**
     * @param Product $model
     */
    public function add(Product $model);

    /**
     * @param Product $model
     */
    public function remove(Product $model);

    /**
     * @param $id
     * @return Product|null
     */
    public function findById($id);

    /**
     * @param Identifier $identifier
     * @return Product|null
     */
    public function findByIdentifier(Identifier $identifier);

    /**
     * @param string $id
     * @return array|null
     */
    public function findSectionsElementsAsArray(string $id);

    /**
     * @param array $ids
     * @return array
     */
    public function findProductCustomPropertiesAsArray(array $ids);

    /**
     * @param array $ids
     * @return array
     */
    public function findSectionCustomPropertiesAsArray(array $ids);

    /**
     * @param array $ids
     * @return array
     */
    public function findElementCustomPropertiesAsArray(array $ids);

    /**
     * @param null|object|array $entity
     */
    public function flush($entity = null);

    /**
     * @param string $id
     * @return int
     */
    public function findNextSectionPosition(string $id);

    /**
     * @param string $productId
     * @param string $sectionId
     * @return int
     */
    public function findNextElementPosition(string $productId, string $sectionId);

    /**
     * @param string $productId
     * @param string|null $seoUrl
     * @return mixed
     */
    public function invalidateCache(string $productId, ?string $seoUrl = null);
}