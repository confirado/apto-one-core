<?php

namespace Apto\Catalog\Application\Backend\Commands\PriceMatrix;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Domain\Core\Model\CustomerGroup\CustomerGroupRepository;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Catalog\Application\Backend\Service\PriceMatrixImport;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrixRepository;
use Money\Currency;

class ImportPriceMatrixHandler implements CommandHandlerInterface
{
    const ALLOWED_EXTENSIONS = [
        // comma separated values
        'csv'
    ];

    /**
     * @var PriceMatrixImport
     */
    protected $priceMatrixImport;

    /**
     * @var PriceMatrixRepository
     */
    protected $priceMatrixRepository;

    /**
     * @var CustomerGroupRepository
     */
    protected $customerGroupRepository;

    /**
     * ImportPriceMatrixHandler constructor.
     * @param PriceMatrixImport $priceMatrixImport
     * @param PriceMatrixRepository $priceMatrixRepository
     * @param CustomerGroupRepository $customerGroupRepository
     */
    public function __construct(PriceMatrixImport $priceMatrixImport, PriceMatrixRepository $priceMatrixRepository, CustomerGroupRepository $customerGroupRepository)
    {
        $this->priceMatrixImport = $priceMatrixImport;
        $this->priceMatrixRepository = $priceMatrixRepository;
        $this->customerGroupRepository = $customerGroupRepository;
    }

    /**
     * @param ImportPriceMatrix $command
     * @throws \Exception
     */
    public function handle(ImportPriceMatrix $command)
    {
        // find price matrix by id
        $priceMatrix = $this->priceMatrixRepository->findById($command->getPriceMatrixId());
        if (null === $priceMatrix) {
            throw new \InvalidArgumentException('A PriceMatrix with id \'' . $command->getPriceMatrixId() . '\' could not be found.');
        }

        // find customer group by id
        $customerGroup = $this->customerGroupRepository->findById($command->getCustomerGroupId());
        if (null === $customerGroup) {
            throw new \InvalidArgumentException('A CustomerGroup with id \'' . $command->getCustomerGroupId() . '\' could not be found.');
        }

        // get currency
        $currency = new Currency($command->getCurrency());

        // set PriceMatrix and CustomerGroup as default for import
        $this->priceMatrixImport
            ->setCurrency($currency)
            ->setPriceMatrix($priceMatrix)
            ->setCustomerGroup($customerGroup)
            ->setCsvType($command->getCsvType());

        if ($command->getCsvType() === 'matrix') {
            $this->priceMatrixImport->setMandatoryColumns([]);
        }
        $errors = [];
        foreach ($command->getFiles() as $path => $filename) {

            // create File for src
            $file = File::createFromPath($path);
            $extension = strtolower(substr($filename, strrpos($filename, '.') + 1));

            // skip forbidden extensions
            if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
                $errors[] = 'Der Dateityp "' . $extension . '" ist ungültig und die Datei "' . $filename . '" wurde ignoriert.';
                continue;
            }

            // run import
            $this->priceMatrixImport->importCsvFile($file, $command->getCsvType());
            $fileErrors = $this->priceMatrixImport->getErrors();
            if (!empty($fileErrors)) {
                $errors[] = 'Während des Imports der Datei "' . $filename . '" traten folgende Fehler auf:' . "\n" . implode("\n", $fileErrors);
            }
        }

        // throw exception if errors occurred
        if (!empty($errors)) {
            throw new \Exception(implode("\n---\n", $errors));
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield ImportPriceMatrix::class => [
            'method' => 'handle',
            'bus' => 'command_bus'
        ];
    }
}