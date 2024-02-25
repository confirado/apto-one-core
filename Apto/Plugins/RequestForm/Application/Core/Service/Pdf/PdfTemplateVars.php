<?php

namespace Apto\Plugins\RequestForm\Application\Core\Service\Pdf;

use Apto\Plugins\RequestForm\Application\Core\Subscribers\ProductInquiry;

class PdfTemplateVars
{
    /**
     * @var array
     */
    private array $templateVars;

    public function __construct()
    {
        $this->templateVars = [];
    }

    /**
     * @param ProductInquiry $productInquiry
     * @return array
     */
    public function getTemplateVars(ProductInquiry $productInquiry): array
    {
        return $this->templateVars;
    }

    /**
     * @param array $templateVars
     */
    public function setTemplateVars(array $templateVars): void
    {
        $this->templateVars = $templateVars;
    }
}
