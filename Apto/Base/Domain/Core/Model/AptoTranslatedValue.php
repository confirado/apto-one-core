<?php

namespace Apto\Base\Domain\Core\Model;

class AptoTranslatedValue implements \JsonSerializable
{

    /**
     * @var array AptoTranslatedValueItem
     */
    protected $translations;

    /**
     * @param array $translations like ['de_DE' => 'Deutsche Beschreibung', 'en_EN' => 'English description']
     * @return AptoTranslatedValue
     */
    public static function fromArray(array $translations): self
    {
        $translatedItems = [];
        foreach ($translations as $locale => $translation) {
            $translatedItems[$locale] = new AptoTranslatedValueItem(
                new AptoLocale($locale),
                $translation
            );
        }

        return new self($translatedItems);
    }

    /**
     * AptoTranslatedValue constructor.
     * @param array $translations
     * @throws InvalidTranslatedValueException
     */
    public function __construct(array $translations)
    {
        foreach ($translations as $isocode => $translation) {
            if (!$translation instanceof AptoTranslatedValueItem) {
                throw new InvalidTranslatedValueException('Given translations are not from Type \'AptoTranslatedValueItem\'.');
            }
            if($translation->getIsocode()->getName() !== $isocode) {
                throw new InvalidTranslatedValueException('Given translation index is not a valid \'AptoLocale\' name value.');
            }
        }

        $this->translations = $translations;
    }

    /**
     * @param AptoLocale $isocode
     * @param AptoLocale $fallback
     * @param bool $fallbackToFirst
     * @return AptoTranslatedValueItem
     */
    public function getTranslation(AptoLocale $isocode, AptoLocale $fallback = null, $fallbackToFirst = false): AptoTranslatedValueItem
    {
        // return the requested translation
        if (array_key_exists($isocode->getName(), $this->translations)) {
            return $this->translations[$isocode->getName()];
        }

        // if set return the requested fallback translation
        if (null !== $fallback && array_key_exists($fallback->getName(), $this->translations)) {
            return $this->translations[$fallback->getName()];
        }

        // return the first found translation
        if (true === $fallbackToFirst) {
            foreach ($this->translations as $translation) {
                return $translation;
            }
        }

        // return an empty translation
        return new AptoTranslatedValueItem($isocode, '');
    }

    /**
     * @param AptoTranslatedValue $value
     * @param AptoTranslatedValueItem $item
     * @return AptoTranslatedValue
     */
    public static function addTranslation(AptoTranslatedValue $value, AptoTranslatedValueItem $item): AptoTranslatedValue
    {
        $isoname = $item->getIsocode()->getName();

        $translations = (array)$value->translations; // php copies arrays by default and does not use references
        $translations[$isoname] = $item;

        return new self($translations);
    }

    /**
     * @param AptoTranslatedValue $value
     * @param AptoLocale $isocode
     * @return AptoTranslatedValue
     */
    public static function deleteTranslation(AptoTranslatedValue $value, AptoLocale $isocode): AptoTranslatedValue
    {
        $isoname = $isocode->getName();

        if (!array_key_exists($isoname, $value->translations)) {
            return $value;
        }

        $translations = (array)$value->translations; // php copies arrays by default and does not use references
        unset($translations[$isoname]);

        return new self($translations);
    }

    /**
     * @return int
     */
    private function countTranslations()
    {
        return count($this->translations);
    }

    /**
     * @param AptoTranslatedValue $aptoTranslatedValue
     * @return AptoTranslatedValue
     */
    public function merge(AptoTranslatedValue $aptoTranslatedValue): AptoTranslatedValue
    {
        return AptoTranslatedValue::fromArray(
            array_merge(
                $this->jsonSerialize(),
                $aptoTranslatedValue->jsonSerialize()
            )
        );
    }

    /**
     * @param AptoTranslatedValue $value
     * @return bool
     */
    public function equals(AptoTranslatedValue $value)
    {
        if ($this->__toString() !== $value->__toString()) {
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->__toArray();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->__toArray(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        $translations = array();
        foreach ($this->translations as $translation) {
            $translationValue = $translation->getValue();
            if (null !== $translationValue) {
                $translations[$translation->getIsocode()->getName()] = $translationValue;
            }
        }

        return $translations;
    }
}
