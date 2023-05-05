<?php

namespace Apto\Base\Application\Core\Query\AptoCustomProperty;

use Apto\Base\Application\Core\QueryHandlerInterface;

class CustomPropertyQueryHandler implements QueryHandlerInterface
{
    /**
     * @var AptoCustomPropertyFinder
     */
    private $aptoCustomPropertyFinder;

    /**
     * CustomPropertyQueryHandler constructor.
     * @param AptoCustomPropertyFinder $aptoCustomPropertyFinder
     */
    public function __construct(AptoCustomPropertyFinder $aptoCustomPropertyFinder)
    {
        $this->aptoCustomPropertyFinder = $aptoCustomPropertyFinder;
    }

    /**
     * @param FindUsedCustomPropertyKeys $query
     * @return array
     */
    public function handleFindUsedCustomPropertyKeys(FindUsedCustomPropertyKeys $query)
    {
        return $this->aptoCustomPropertyFinder->findUsedKeys();
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindUsedCustomPropertyKeys::class => [
            'method' => 'handleFindUsedCustomPropertyKeys',
            'bus' => 'query_bus'
        ];
    }
}