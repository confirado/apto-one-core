<?php

namespace Apto\Base\Application\Core\Service\Translation;

class TranslationRegistry
{
    /**
     * @var array
     */
    private $translationExportProviders;

    /**
     * @var array
     */
    private $translationImportProviders;

    /**
     * TranslationRegistry constructor.
     */
    public function __construct()
    {
        $this->translationExportProviders = [];
        $this->translationImportProviders = [];
    }

    /**
     * @param TranslationExportProvider $translationExportProvider
     */
    public function addTranslationExportProvider(TranslationExportProvider $translationExportProvider)
    {
        $className = get_class($translationExportProvider);

        if (array_key_exists($className, $this->translationExportProviders)) {
            throw new \InvalidArgumentException('A TranslationExport provider with an id \'' . $className . '\' is already registered.');
        }

        $this->translationExportProviders[$className] = $translationExportProvider;
    }
    
    /**
     * @param TranslationImportProvider $translationImportProvider
     */
    public function addTranslationImportProvider(TranslationImportProvider $translationImportProvider)
    {
        $className = get_class($translationImportProvider);

        if (array_key_exists($className, $this->translationImportProviders)) {
            throw new \InvalidArgumentException('A TranslationImport provider with an id \'' . $className . '\' is already registered.');
        }

        $this->translationImportProviders[$className] = $translationImportProvider;
    }

    /**
     * @return array
     */
    public function getTranslationExportProviders(): array
    {
        return $this->translationExportProviders;
    }

    /**
     * @return array
     */
    public function getTranslationImportProviders(): array
    {
        return $this->translationImportProviders;
    }

    /**
     * @param string $type
     * @return TranslationImportProvider
     * @throws Exceptions\TranslationTypeNotFoundException
     */
    public function getTranslationImportProviderByType(string $type)
    {
        /** @var TranslationImportProvider $importProvider */
        foreach ($this->translationImportProviders as $importProvider) {
            if ($importProvider->getType() === $type) {
                return $importProvider;
            }
        }
        throw new Exceptions\TranslationTypeNotFoundException($type);
    }
}
