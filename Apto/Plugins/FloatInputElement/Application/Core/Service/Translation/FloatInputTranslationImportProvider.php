<?php

namespace Apto\Plugins\FloatInputElement\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\AbstractSpecialElementTranslationImportProvider;

class FloatInputTranslationImportProvider extends AbstractSpecialElementTranslationImportProvider
{
    const TRANSLATABLE_FIELDS = ['prefix', 'suffix', 'livePricePrefix', 'livePriceSuffix'];
    const TRANSLATION_TYPE = 'FloatInputElement';
}
