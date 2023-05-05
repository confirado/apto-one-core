<?php

namespace Apto\Base\Domain\Core\Model\Language;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;

class Language extends AptoAggregate
{

    /**
     * @var AptoTranslatedValue
     */
    protected $name;

    /**
     * @var AptoLocale
     */
    protected $isocode;

    /**
     * Language constructor.
     * @param AptoUuid $id
     * @param $name AptoTranslatedValue
     * @param $isocode AptoLocale
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $name, AptoLocale $isocode)
    {
        parent::__construct($id);
        $this->publish(
            new LanguageAdded($id)
        );
        $this
            ->setName($name)
            ->setIsocode($isocode);
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getName(): AptoTranslatedValue
    {
        return $this->name;
    }

    /**
     * @param $name AptoTranslatedValue
     * @return Language
     */
    public function setName(AptoTranslatedValue $name): Language
    {
        if (null !== $this->name && $this->getName()->equals($name)) {
            return $this;
        }
        $this->name = $name;
        $this->publish(
            new LanguageNameUpdated(
                $this->getId(),
                $this->getName()
            )
        );
        return $this;
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
     * @return Language
     */
    public function setIsocode(AptoLocale $isocode): Language
    {
        if (null !== $this->isocode && $this->getIsocode()->equals($isocode)) {
            return $this;
        }
        $this->isocode = $isocode;
        $this->publish(
            new LanguageIsocodeUpdated(
                $this->getId(),
                $this->getIsocode()
            )
        );
        return $this;
    }
}