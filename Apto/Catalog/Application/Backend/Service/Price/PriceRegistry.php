<?php

namespace Apto\Catalog\Application\Backend\Service\Price;

class PriceRegistry
{
    /**
     * @var array
     */
    private $priceExportProviders;

    /**
     * @var array
     */
    private $priceImportProviders;

    /**
     * PriceRegistry constructor.
     */
    public function __construct()
    {
        $this->priceExportProviders = [];
        $this->priceImportProviders = [];
    }

    /**
     * @param PriceExportProvider $priceExportProvider
     */
    public function addPriceExportProvider(PriceExportProvider $priceExportProvider)
    {
        $className = get_class($priceExportProvider);

        if (array_key_exists($className, $this->priceExportProviders)) {
            throw new \InvalidArgumentException('A PriceExport provider with an id \'' . $className . '\' is already registered.');
        }

        $this->priceExportProviders[$className] = $priceExportProvider;
    }

    /**
     * @param PriceImportProvider $priceImportProvider
     */
    public function addPriceImportProvider(PriceImportProvider $priceImportProvider)
    {
        $className = get_class($priceImportProvider);

        if (array_key_exists($className, $this->priceImportProviders)) {
            throw new \InvalidArgumentException('A PriceImport provider with an id \'' . $className . '\' is already registered.');
        }

        $this->priceImportProviders[$className] = $priceImportProvider;
    }

    /**
     * @return array
     */
    public function getPriceExportProviders(): array
    {
        return $this->priceExportProviders;
    }

    /**
     * @return array
     */
    public function getPriceImportProviders(): array
    {
        return $this->priceImportProviders;
    }

    /**
     * @param string $type
     * @return PriceImportProvider
     * @throws Exceptions\PriceTypeNotFoundException
     */
    public function getPriceImportProviderByType(string $type)
    {
        /** @var PriceImportProvider $importProvider */
        foreach ($this->priceImportProviders as $importProvider) {
            if ($importProvider->getType() === $type) {
                return $importProvider;
            }
        }
        throw new Exceptions\PriceTypeNotFoundException($type);
    }
}
