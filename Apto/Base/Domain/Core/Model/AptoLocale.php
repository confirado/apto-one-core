<?php

namespace Apto\Base\Domain\Core\Model;

class AptoLocale
{
    /**
     * validates against an ISO 639-1 / ISO 3166-1 standard language code, e.g.
     * de, de_AT, en, en_US, en_GB etc., case insensitive
     */
    const REGEXP_VALID_ISOCODE = '/^([a-z]{2})(([_-])([A-Z]{2}))?$/i';

    /**
     * @var string
     */
    protected $name;

    /**
     * AptoLocale constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param AptoLocale $aptoLocale
     * @return bool
     */
    public function equals(AptoLocale $aptoLocale): bool
    {
        if($this->getName() == $aptoLocale->getName()) {
            return true;
        }
        return false;
    }

    /**
     * @param string $name
     * @return AptoLocale
     * @throws InvalidAptoLocaleException
     */
    private function setName(string $name): AptoLocale
    {
        $matches = array();
        if ('' === $name || strlen($name) > 5 || 1 != preg_match(self::REGEXP_VALID_ISOCODE, $name, $matches)) {
            throw new InvalidAptoLocaleException('Given name is not a valid isocode.');
        }

        $this->name = strtolower($matches[1]);
        if (count($matches) >= 4) {
            $this->name .= '_' . strtoupper($matches[4]);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}