<?php

namespace Apto\Plugins\FloatInputElement\Application\Core\Query\FloatInputItem;

use Apto\Base\Application\Core\QueryHandlerInterface;

class FloatInputItemQueryHandler implements QueryHandlerInterface
{
    /**
     * @var FloatInputItemFinder
     */
    protected $floatInputItemFinder;

    /**
     * FloatInputItemQueryHandler constructor.
     * @param FloatInputItemFinder $floatInputItemFinder
     */
    public function __construct(FloatInputItemFinder $floatInputItemFinder)
    {
        $this->floatInputItemFinder = $floatInputItemFinder;
    }

    /**
     * @param FindFloatInputPrices $query
     * @return array
     */
    public function handleFindFloatInputPrices(FindFloatInputPrices $query)
    {
        return $this->floatInputItemFinder->findPrices($query->getId());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindFloatInputPrices::class => [
            'method' => 'handleFindFloatInputPrices',
            'bus' => 'query_bus'
        ];
    }
}