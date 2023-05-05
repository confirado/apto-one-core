<?php

namespace Apto\Plugins\SelectBoxElement\Application\Core\Query\SelectBoxItem;

use Apto\Base\Application\Core\QueryHandlerInterface;

class SelectBoxItemQueryHandler implements QueryHandlerInterface
{
    /**
     * @var SelectBoxItemFinder
     */
    protected $selectBoxItemFinder;

    /**
     * SelectBoxItemQueryHandler constructor.
     * @param SelectBoxItemFinder $selectBoxItemFinder
     */
    public function __construct(SelectBoxItemFinder $selectBoxItemFinder)
    {
        $this->selectBoxItemFinder = $selectBoxItemFinder;
    }

    /**
     * @param FindSelectBoxItems $query
     * @return array
     */
    public function handleFindSelectBoxItems(FindSelectBoxItems $query)
    {
        return $this->selectBoxItemFinder->findByElementId($query->getElementId());
    }

    /**
     * @param FindSelectBoxItem $query
     * @return array|null
     */
    public function handleFindSelectBoxItem(FindSelectBoxItem $query)
    {
        return $this->selectBoxItemFinder->findById($query->getId());
    }

    /**
     * @param FindSelectBoxItemPrices $query
     * @return array|null
     */
    public function handleFindSelectBoxItemPrices(FindSelectBoxItemPrices $query)
    {
        return $this->selectBoxItemFinder->findPrices($query->getId());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindSelectBoxItems::class => [
            'method' => 'handleFindSelectBoxItems',
            'bus' => 'query_bus'
            ];

        yield FindSelectBoxItem::class => [
            'method' => 'handleFindSelectBoxItem',
            'bus' => 'query_bus'
        ];

        yield FindSelectBoxItemPrices::class => [
            'method' => 'handleFindSelectBoxItemPrices',
            'bus' => 'query_bus'
        ];
    }
}