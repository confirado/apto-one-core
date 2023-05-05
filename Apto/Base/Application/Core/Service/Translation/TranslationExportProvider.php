<?php

namespace Apto\Base\Application\Core\Service\Translation;

interface TranslationExportProvider
{
    /**
     * @return array[TranslationItem]
     */
    public function getTranslatedValues(): array;
}