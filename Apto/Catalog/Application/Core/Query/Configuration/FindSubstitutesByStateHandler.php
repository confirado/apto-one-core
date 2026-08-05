<?php

namespace Apto\Catalog\Application\Core\Query\Configuration;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Catalog\Application\Core\Service\Substitute\SubstituteResolverService;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class FindSubstitutesByStateHandler implements QueryHandlerInterface
{
    /**
     * @var SubstituteResolverService
     */
    protected $substituteResolverService;

    /**
     * @param SubstituteResolverService $substituteResolverService
     */
    public function __construct(SubstituteResolverService $substituteResolverService)
    {
        $this->substituteResolverService = $substituteResolverService;
    }

    /**
     * @param FindSubstitutesByState $query
     * @return string
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     * @throws \Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException
     */
    public function handleFindSubstitutesByState(FindSubstitutesByState $query): string
    {
        $state = new State($query->getState());

        return $this->substituteResolverService->resolveComputedValueSubstitutes(
            $query->getPreResolvedText(),
            $state,
            $query->getProductId()
        );
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindSubstitutesByState::class => [
            'method' => 'handleFindSubstitutesByState',
            'bus' => 'query_bus'
        ];
    }
}
