<?php

namespace Apto\Base\Application\Core\Service\Translation;

interface TranslationImportProvider
{
    /**
     * @param TranslationItem $translationItem
     */
    public function setTranslatedValue(TranslationItem $translationItem): void;

    /**
     * @return string
     */
    public function getType(): string;
}