<?php

namespace Apto\Catalog\Application\Backend\Query\Price\Exceptions;

class ConflictingPriceMatrixException extends \Exception
{
    /**
     * ConflictingPriceMatrixException constructor.
     * @param $priceMatrixIds
     */
    public function __construct($priceMatrixIds)
    {
        $priceMatrixIds = implode(',', $priceMatrixIds);
        $message = 'Die PriceMatrizen ' . $priceMatrixIds . ' werden in Produkten verwendet, die nicht für die Änderung vorgesehen sind. Vorgang abgebrochen!';
        parent::__construct($message);
    }
}
