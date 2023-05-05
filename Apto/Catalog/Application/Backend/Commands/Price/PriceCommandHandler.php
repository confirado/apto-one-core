<?php

namespace Apto\Catalog\Application\Backend\Commands\Price;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Service\Math\Calculator;
use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotFoundException;
use Apto\Catalog\Application\Backend\Service\Price\PriceItem;
use Apto\Catalog\Application\Backend\Service\Price\PriceRegistry;

class PriceCommandHandler implements CommandHandlerInterface
{
    /**
     * @var PriceRegistry
     */
    private $priceRegistry;

    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * @var array
     */
    private $errors;

    /**
     * PriceCommandHandler constructor.
     * @param PriceRegistry $priceRegistry
     */
    public function __construct(PriceRegistry $priceRegistry)
    {
        $this->priceRegistry = $priceRegistry;
        $this->calculator = new Calculator();
    }

    /**
     * @param SetPrices $command
     * @throws InvalidUuidException
     * @throws PriceTypeNotFoundException
     */
    public function handleSetPrices(SetPrices $command)
    {
        $priceItems = $command->getPriceItems();
        foreach($priceItems as $priceItem) {
            $priceImportProvider = $this->priceRegistry->getPriceImportProviderByType($priceItem['priceType']);
            $priceItem = PriceItem::fromArray($priceItem, $command->getMultiplier());
            $priceImportProvider->setPrice($priceItem);
        }
    }

    /**
     * @param SetPricesByFormula $command
     * @throws InvalidUuidException
     * @throws PriceTypeNotFoundException
     */
    public function handleSetPricesByFormula(SetPricesByFormula $command)
    {
        $priceItems = $command->getPriceItems();
        foreach($priceItems as $priceItem) {
            $priceImportProvider = $this->priceRegistry->getPriceImportProviderByType($priceItem['priceType']);
            $priceItem['money']['amount'] = $this->calculator->round(
                (string) math_eval(
                    $command->getFormula(),
                    [
                        'x' => $priceItem['money']['amount']
                    ]
                )
            );
            $priceItem = PriceItem::fromArray($priceItem);
            $priceImportProvider->setPrice($priceItem);
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield SetPrices::class => [
            'method' => 'handleSetPrices',
            'aptoMessageName' => 'BatchManipulationSetPrices',
            'bus' => 'command_bus'
        ];

        yield SetPricesByFormula::class => [
            'method' => 'handleSetPricesByFormula',
            'aptoMessageName' => 'BatchManipulationSetPricesByFormula',
            'bus' => 'command_bus'
        ];
    }
}