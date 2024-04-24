<p align='center'><a href="https://apto.one/" target="_blank" rel="noopener noreferrer">
    <img width="250" src="https://www.confirado.de/files/images/confirado/logos/Apto.ONE/logo_apto_blau_gr%C3%BCn.png">
</a></p>

## Apto.ONE is build up of the following components:

- Backend (PHP + Symfony)
- Administration Frontend (AngularJS, AngularJS Material)
- End-User Frontend (Angular 14+)

You can find our Documentation here: https://docs.apto.one

## Installation

add the following lines to your config/bundles.php file

    Apto\Base\Infrastructure\AptoBaseBundle\AptoBaseBundle::class => ['all' => true],
    Apto\Catalog\Infrastructure\AptoCatalogBundle\AptoCatalogBundle::class => ['all' => true],
    Apto\Plugins\AreaElement\Infrastructure\AreaElementBundle\AreaElementBundle::class => ['all' => true],
    Apto\Plugins\CustomText\Infrastructure\CustomTextBundle\CustomTextBundle::class => ['all' => true],
    Apto\Plugins\PartsListElement\Infrastructure\PartsListElementBundle\PartsListElementBundle::class => ['all' => true],
    Apto\Plugins\FileUpload\Infrastructure\FileUploadBundle\FileUploadBundle::class => ['all' => true],
    Apto\Plugins\FloatInputElement\Infrastructure\FloatInputElementBundle\FloatInputElementBundle::class => ['all' => true],
    Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\MaterialPickerElementBundle::class => ['all' => true],
    Apto\Plugins\PricePerUnitElement\Infrastructure\PricePerUnitElementBundle\PricePerUnitElementBundle::class => ['all' => true],
    Apto\Plugins\SelectBoxElement\Infrastructure\SelectBoxElementBundle\SelectBoxElementBundle::class => ['all' => true],
    Apto\Plugins\WidthHeightElement\Infrastructure\WidthHeightElementBundle\WidthHeightElementBundle::class => ['all' => true],
    Apto\Plugins\DateElement\Infrastructure\DateElementBundle\DateElementBundle::class => ['all' => true],
    Apto\Plugins\RequestForm\Infrastructure\RequestFormBundle\RequestFormBundle::class => ['all' => true],

