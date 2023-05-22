<?php

namespace Apto\Catalog\Application\Backend\Query\Price;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Catalog\Application\Backend\Service\Price\PriceExportProvider;
use Apto\Catalog\Application\Backend\Service\Price\PriceRegistry;

class PriceQueryHandler implements QueryHandlerInterface
{
    /**
     * @var PriceRegistry
     */
    private $priceRegistry;

    /**
     * PriceQueryHandler constructor.
     * @param PriceRegistry $priceRegistry
     */
    public function __construct(PriceRegistry $priceRegistry)
    {
        $this->priceRegistry = $priceRegistry;
    }

    /**
     * @param FindPrices $query
     * @return array
     * @throws \Exception
     */
    public function handleFindPrices (FindPrices $query): array
    {
        $prices = [];
        /** @var PriceExportProvider $priceExportProvider */
        foreach ($this->priceRegistry->getPriceExportProviders() as $priceExportProvider) {
            $filter = [];
            if (array_key_exists($priceExportProvider->getType(), $query->getFilter())) {
                $filter = $query->getFilter()[$priceExportProvider->getType()];
            }

            $prices = array_merge($prices, $priceExportProvider->getPrices($query->getProductIds(), $filter));
        }

        return $prices;
    }

    /**
     * @param FindPriceMatrixConflicts $query
     * @return bool
     */
    public function handleFindPriceMatrixConflicts(FindPriceMatrixConflicts $query)
    {
        foreach ($this->priceRegistry->getPriceExportProviders() as $priceExportProvider) {
            if ($priceExportProvider->getType() === 'PriceMatrix') {
                $conflictingPriceMatrices = $priceExportProvider->getMatricesUsedInOtherProducts($query->getProductIds());
                if (count($conflictingPriceMatrices) > 0) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param FindPriceMatrixIdsByProductIds $query
     * @return array|bool
     */
    public function handleFindPriceMatrixIdsByProductIds(FindPriceMatrixIdsByProductIds $query)
    {
        foreach ($this->priceRegistry->getPriceExportProviders() as $priceExportProvider) {
            if ($priceExportProvider->getType() === 'PriceMatrix') {
                return $priceExportProvider->getPriceMatrixIdsOfProductElements($query->getProductIds());
            }
        }
        return [];
    }

    /**
     * @param FindPricesByPriceMatrixIds $query
     * @return array|bool
     */
    public function handleFindPricesByPriceMatrixIds(FindPricesByPriceMatrixIds $query)
    {
        foreach ($this->priceRegistry->getPriceExportProviders() as $priceExportProvider) {
            if ($priceExportProvider->getType() === 'PriceMatrix') {
                return $priceExportProvider->getPricesByPriceMatrixIds($query->getPriceMatrixIds(), $query->getFilter());
            }
        }
        return [];
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindPriceMatrixConflicts::class => [
            'method' => 'handleFindPriceMatrixConflicts',
            'aptoMessageName' => 'BatchManipulationFindPriceMatrixConflicts',
            'bus' => 'query_bus'
        ];

        yield FindPriceMatrixIdsByProductIds::class => [
            'method' => 'handleFindPriceMatrixIdsByProductIds',
            'aptoMessageName' => 'BatchManipulationFindPriceMatrixIdsByProductIds',
            'bus' => 'query_bus'
        ];

        yield FindPrices::class => [
            'method' => 'handleFindPrices',
            'aptoMessageName' => 'BatchManipulationFindPrices',
            'bus' => 'query_bus'
        ];

        yield FindPricesByPriceMatrixIds::class => [
            'method' => 'handleFindPricesByPriceMatrixIds',
            'aptoMessageName' => 'BatchManipulationFindPricesByPriceMatrixIds',
            'bus' => 'query_bus'
        ];
    }
}
