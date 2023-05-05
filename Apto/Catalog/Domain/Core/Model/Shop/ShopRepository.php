<?php
namespace Apto\Catalog\Domain\Core\Model\Shop;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface ShopRepository extends AptoRepository
{
    /**
     * @param Shop $model
     */
    public function update(Shop $model);

    /**
     * @param Shop $model
     */
    public function add(Shop $model);

    /**
     * @param Shop $model
     */
    public function remove(Shop $model);

    /**
     * @param string $id
     * @return Shop|null
     */
    public function findById($id);

    /**
     * @param string $domain
     * @return Shop|null
     */
    public function findOneByDomain(string $domain);

    /**
     * @param string $domain
     * @return array|null
     */
    public function findConnectorConfigByDomain(string $domain);
}