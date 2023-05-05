<?php

namespace Apto\Plugins\WidthHeightElement\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\AbstractSpecialElementTranslationImportProvider;

class WidthHeightElementTranslationImportProvider extends AbstractSpecialElementTranslationImportProvider
{
    const TRANSLATABLE_FIELDS = ['prefixWidth', 'prefixHeight', 'suffixWidth', 'suffixHeight'];
    const TRANSLATION_TYPE = 'WidthHeightElement';
}
