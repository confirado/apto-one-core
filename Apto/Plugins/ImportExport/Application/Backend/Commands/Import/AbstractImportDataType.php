<?php

namespace Apto\Plugins\ImportExport\Application\Backend\Commands\Import;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractImportDataType implements CommandInterface
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var array
     */
    private $fields;

    /**
     * ImportDefaultDataType constructor.
     * @param string $locale
     * @param string $domain
     * @param array $fields
     */
    public function __construct(string $locale, string $domain, array $fields)
    {
        $this->locale = $locale;
        $this->domain = $domain;
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}