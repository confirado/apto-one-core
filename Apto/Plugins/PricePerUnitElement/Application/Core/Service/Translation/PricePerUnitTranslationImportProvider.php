<?php

namespace Apto\Plugins\PricePerUnitElement\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\AbstractSpecialElementTranslationImportProvider;

class PricePerUnitTranslationImportProvider extends AbstractSpecialElementTranslationImportProvider
{
    const TRANSLATABLE_FIELDS = ['textBoxPrefix', 'textBoxSuffix', 'livePricePrefix', 'livePriceSuffix'];
    const TRANSLATION_TYPE = 'PricePerUnitElement';
}
