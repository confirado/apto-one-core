<?php

namespace Apto\Catalog\Application\Core\Query\Configuration;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Application\Core\Service\StatePrice\StatePriceService;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Base\Domain\Core\Model\InvalidUuidException;

class FindPriceByStateHandler implements QueryHandlerInterface
{
    /**
     * @var StatePriceService
     */
    private $statePriceService;

    /**
     * FindPriceByStateHandler constructor.
     * @param StatePriceService $statePriceService
     */
    public function __construct(StatePriceService $statePriceService)
    {
        $this->statePriceService = $statePriceService;
    }

    /**
     * @param FindPriceByState $query
     * @return array
     * @throws InvalidUuidException
     */
    public function handleFindPriceByState(FindPriceByState $query)
    {
        return $this->statePriceService->getStatePrice(
            new AptoUuid($query->getProductId()),
            new State($query->getState()),
            new AptoLocale($query->getLocale()),
            $query->getShopCurrency(),
            $query->getDisplayCurrency(),
            $query->getCustomerGroupExternalId(),
            $query->getSessionCookies(),
            $query->getTaxState(),
            $query->getConnectorUser()
        );
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindPriceByState::class => [
            'method' => 'handleFindPriceByState',
            'bus' => 'query_bus'
        ];
    }
}
