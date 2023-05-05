<?php

namespace Apto\Base\Domain\Core\Model;

class AptoTranslatedValueItem
{

    /**
     * @var AptoLocale
     */
    protected $isocode;

    /**
     * @var string
     */
    protected $value;

    /**
     * AptoTranslatedValueItem constructor.
     * @param AptoLocale $isocode
     * @param string $value
     */
    public function __construct(AptoLocale $isocode, string $value)
    {
        $this
            ->setIsocode($isocode)
            ->setValue($value);
    }

    /**
     * @return AptoLocale
     */
    public function getIsocode(): AptoLocale
    {
        return $this->isocode;
    }

    /**
     * @param AptoLocale $isocode
     * @return AptoTranslatedValueItem
     */
    private function setIsocode(AptoLocale $isocode): AptoTranslatedValueItem
    {
        $this->isocode = $isocode;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return AptoTranslatedValueItem
     */
    private function setValue(string $value): AptoTranslatedValueItem
    {
        $this->value = $value;
        return $this;
    }
}