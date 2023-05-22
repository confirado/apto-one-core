<?php

namespace Apto\Plugins\RequestForm\Application\Core\Subscribers;

use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\MpdfException;

use Apto\Base\Application\Core\Service\TemplateRendererInterface;
use Apto\Base\Application\Core\Service\TemplateMailerInterface;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;
use Apto\Base\Application\Core\Query\ContentSnippet\ContentSnippetFinder;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Application\Core\EventHandlerInterface;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Catalog\Application\Core\Service\ShopConnector\BasketItem;

use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Apto\Catalog\Application\Core\Service\StatePrice\StatePriceService;
use Apto\Catalog\Application\Frontend\Events\Configuration\ConfigurationFinished;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Application\Core\Query\Shop\ShopFinder;

use Apto\Plugins\RequestForm\Domain\Core\Model\OfferData\OfferData;
use Apto\Plugins\RequestForm\Domain\Core\Model\OfferData\OfferDataRepository;
use Apto\Plugins\RequestForm\Domain\Core\Model\OfferHtml\OfferHtml;
use Apto\Plugins\RequestForm\Domain\Core\Model\OfferHtml\OfferHtmlRepository;
use Apto\Plugins\RequestForm\Domain\Core\Model\RandomNumber\RandomNumberRepository;
use Apto\Plugins\RequestForm\Domain\Core\Model\RandomNumber\RandomNumber;
use Apto\Plugins\RequestForm\Domain\Core\Model\RandomNumber\AllRandomNumbersHasUsedException;
use Apto\Plugins\FileUpload\Domain\Core\Model\Service\Converter\MimeTypeExtensionConverter;

class SendProductInquiryHandler implements EventHandlerInterface
{
    /**
     * @var RequestStore
     */
    protected $requestSessionStore;

    /**
     * @var RandomNumberRepository
     */
    private $randomNumberRepository;

    /**
     * @var OfferHtmlRepository
     */
    private $offerHtmlRepository;

    /**
     * @var OfferDataRepository
     */
    private $offerDataRepository;

    /**
     * @var ProductFinder
     */
    protected $productFinder;

    /**
     * @var ContentSnippetFinder
     */
    private $contentSnippetFinder;

    /**
     * @var StatePriceService
     */
    protected $statePriceService;

    /**
     * @var TemplateMailerInterface
     */
    protected $templateMailer;

    /**
     * @var TemplateRendererInterface
     */
    private $templateRenderer;

    /**
     * @var MediaFileSystemConnector
     */
    private $mediaFileSystemConnector;

    /**
     * @var array|null
     */
    private $config;

    /**
     * @var string
     */
    private $mediaRelativePath;

    /**
     * @var bool
     */
    private $sendPDFToCustomer;

    /**
     * @var string|int
     */
    private $randomNumber;

    /**
     * @var mixed|string
     */
    private $randomNumberPrefix;

    /**
     * @var bool
     */
    private $prioritySeperate;

    /**
     * @var bool
     */
    private $priorityOnly;

    /**
     * @var string
     */
    private $partnerMail;

    /**
     * @var string
     */
    private $partnerName;

    /**
     * @var string
     */
    private $partnerID;

    /**
     * @var BasketItem|null
     */
    private $basketItem;

    /**
     * @var array
     */
    private $prices;

    /**
     * @var array
     */
    private $elementAttachments;

    /**
     * @var array
     */
    private $contentSnippets;

    /**
     * @var array|null
     */
    protected $shop;

    /**
     * @param RequestStore $requestSessionStore
     * @param RandomNumberRepository $randomNumberRepository
     * @param OfferHtmlRepository $offerHtmlRepository
     * @param OfferDataRepository $offerDataRepository
     * @param ShopFinder $shopFinder
     * @param ProductFinder $productFinder
     * @param ContentSnippetFinder $contentSnippetFinder
     * @param StatePriceService $statePriceService
     * @param TemplateMailerInterface $templateMailer
     * @param TemplateRendererInterface $templateRenderer
     * @param AptoParameterInterface $aptoParameter
     * @param MediaFileSystemConnector $mediaFileSystemConnector
     */
    public function __construct(
        RequestStore $requestSessionStore,
        RandomNumberRepository $randomNumberRepository,
        OfferHtmlRepository $offerHtmlRepository,
        OfferDataRepository $offerDataRepository,
        ShopFinder $shopFinder,
        ProductFinder $productFinder,
        ContentSnippetFinder $contentSnippetFinder,
        StatePriceService $statePriceService,
        TemplateMailerInterface $templateMailer,
        TemplateRendererInterface $templateRenderer,
        AptoParameterInterface $aptoParameter,
        MediaFileSystemConnector $mediaFileSystemConnector
    ) {
        $this->requestSessionStore = $requestSessionStore;
        $this->randomNumberRepository = $randomNumberRepository;
        $this->offerHtmlRepository = $offerHtmlRepository;
        $this->offerDataRepository = $offerDataRepository;
        $this->productFinder = $productFinder;
        $this->contentSnippetFinder = $contentSnippetFinder;
        $this->statePriceService = $statePriceService;
        $this->templateMailer = $templateMailer;
        $this->templateRenderer = $templateRenderer;
        $this->mediaFileSystemConnector = $mediaFileSystemConnector;
        $this->contentSnippets = $this->contentSnippetFinder->getTree(true, $this->requestSessionStore->getHttpHost());

        // params
        $this->mediaRelativePath = $aptoParameter->get('media_relative_path');
        $this->shop = $shopFinder->findContextByDomain($this->requestSessionStore->getHttpHost());
        $this->initConfig($requestSessionStore, $aptoParameter);
        $this->assertValidConfig();

        // config
        $this->sendPDFToCustomer = true;
        $this->randomNumber = 0;
        $this->randomNumberPrefix = '';
        if (array_key_exists('randomNumberPrefix', $this->config)) {
            $this->randomNumberPrefix = $this->config['randomNumberPrefix'];
        }
        $this->prioritySeperate = false;
        $this->priorityOnly = false;

        // Default Partner Id
        $this->partnerMail = "";
        $this->partnerName = "";
        $this->partnerID = "confirado";

        $this->basketItem = null;
        $this->prices = [];

        // element attachments
        $this->elementAttachments = [];
    }

    /**
     * @return bool
     */
    private function isDisabled(): bool
    {
        if (null === $this->shop) {
            return true;
        }

        foreach ($this->shop['customProperties'] as $customProperty) {
            if ($customProperty['key'] === 'requestForm' && $customProperty['value'] === 'disabled') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param RequestStore $requestSessionStore
     * @param AptoParameterInterface $aptoParameter
     * @return void
     */
    private function initConfig(RequestStore $requestSessionStore, AptoParameterInterface $aptoParameter)
    {
        if (!$aptoParameter->has('apto_plugin_request_form')) {
            $this->config = null;
            return;
        }

        $host = $requestSessionStore->getHttpHost();
        $config = $aptoParameter->get('apto_plugin_request_form');
        $domainConfig = [];

        if (array_key_exists('domain_config', $config) && array_key_exists($host, $config['domain_config'])) {
            $domainConfig = $config['domain_config'][$host];
        }

        if (array_key_exists('domain_config', $config)) {
            unset($config['domain_config']);
        }

        $this->config = array_replace_recursive($config, $domainConfig);
    }

    /**
     * @param array $productSection
     * @param array $state
     * @param string $locale
     */
    private function initPartnerInfo(array $productSection, array $state, string $locale)
    {
        foreach ($productSection as $section) {
            $sectionId = $section['id'];
            if ($this->idInArray($sectionId, $state)) {
                foreach ($section['elements'] as $element) {
                    $elementId = $element['id'];
                    if ($this->idInArray($elementId, $state[$section['id']])) {
                        foreach ($element['customProperties'] as $customProperty) {
                            if($customProperty['key'] === 'partnerMail') {
                                $this->partnerMail = $customProperty['value'][$locale];
                            }
                            if($customProperty['key'] === 'partnerName') {
                                $this->partnerName = $customProperty['value'][$locale];
                            }
                            if($customProperty['key'] === 'partnerID') {
                                $this->partnerID = $customProperty['value'];
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $product
     * @param State $state
     * @param AptoLocale $locale
     * @return void
     * @throws InvalidUuidException
     */
    private function initElementAttachments(array $product, State $state, AptoLocale $locale)
    {
        if (!$this->hasAdminElementAttachments() && !$this->hasCustomerElementAttachments()) {
            return;
        }

        $mimeTypeExtensionConverter = new MimeTypeExtensionConverter();
        $elementAttachments = [];

        foreach ($product['sections'] as $section) {
            $sectionId = new AptoUuid($section['id']);

            foreach ($section['elements'] as $element) {
                $elementId = new AptoUuid($element['id']);

                if (!$state->isElementActive($sectionId, $elementId)) {
                    continue;
                }
                $elementAttachments = array_merge($elementAttachments, $element['attachments']);
            }
        }

        foreach ($elementAttachments as $elementAttachment) {
            // set media file
            $mediaFile = $elementAttachment['mediaFile'][0];

            // do not add same media files twice
            if (array_key_exists($mediaFile['id'], $this->elementAttachments)) {
                continue;
            }

            // set mime type for attachment
            $mimeType = null;
            $extensionMimeTypes = $mimeTypeExtensionConverter->extensionToMimeTypes($mediaFile['extension']);
            if (count($extensionMimeTypes) > 0) {
                $mimeType = $extensionMimeTypes[0];
            }

            // add attachment
            $attachmentPath = $mediaFile['path'] . '/' . $mediaFile['filename'] . '.' . $mediaFile['extension'];
            $attachmentName = AptoTranslatedValue::fromArray($elementAttachment['name'])->getTranslation($locale, new AptoLocale('de_DE'), true)->getValue();

            $this->elementAttachments[$mediaFile['id']] = [
                'name' => $attachmentName,
                'path' => $this->mediaFileSystemConnector->getAbsolutePath($attachmentPath),
                'contentType' => $mimeType
            ];
        }

        $this->elementAttachments = array_values($this->elementAttachments);
    }

    /**
     * @param ConfigurationFinished $event
     * @return void
     * @throws AllRandomNumbersHasUsedException
     * @throws InvalidUuidException
     * @throws MpdfException
     */
    public function onConfigurationFinished(ConfigurationFinished $event)
    {
        if ($this->config === null || $this->isDisabled()) {
            return;
        }

        $this->basketItem = $event->getBasketItem();
        $productInquiry = new ProductInquiry(
            $event->getBasketItem(),
            $event->getQuantity()
        );
        $locale = new AptoLocale($productInquiry->getLocale());

        // get prices
        $this->prices = $this->statePriceService->getStatePrice(
            new AptoUuid($productInquiry->getProductId()),
            new State($productInquiry->getCompressedState()),
            $locale,
            $productInquiry->getShopCurrency(),
            $productInquiry->getDisplayCurrency(),
            $productInquiry->getCustomerGroupExternalId()
        );

        // Generate Offer random number
        $this->setRandomNumber();

        // Store Data
        if(array_key_exists('saveOfferData', $this->config) && $this->config['saveOfferData']){
            $formData = $productInquiry->getFormData();
            $customer = $formData['customer'];
            $this->storeData($customer, $productInquiry->getState());
        }

        $this->sendMail($productInquiry);
    }

    /**
     * @param ProductInquiry $productInquiry
     * @return false|void
     * @throws InvalidUuidException
     * @throws MpdfException
     */
    protected function sendMail(ProductInquiry $productInquiry)
    {
        // get customer
        $formData = $productInquiry->getFormData();
        $customer = $formData['customer'];

        // set locale
        $locale = new AptoLocale($productInquiry->getLocale());

        // get product
        $product = $this->productFinder->findConfigurableProductById($productInquiry->getProductId());
        if (null === $product) {
            return false;
        }

        // init element attachments
        $this->initElementAttachments($product, new State($productInquiry->getCompressedState()), $locale);

        // init Partner info
        $this->initPartnerInfo($product['sections'], $productInquiry->getCompressedState(), $locale->getName());

        // generate pdf
        $pdf = '';
        $adminPdf = '';
        $customerPdf = '';

        if (
            array_key_exists('generateAdminPDF', $this->config) && $this->config['generateAdminPDF'] ||
            array_key_exists('generateUserPDF', $this->config) && $this->config['generateUserPDF']
        ) {
            $pdf = $this->generatePDF($product, $formData, $customer, $productInquiry);
        }

        // set admin pdf
        if (array_key_exists('generateAdminPDF', $this->config) && $this->config['generateAdminPDF']) {
            $adminPdf = $pdf;
        }

        // set customer pdf
        if (array_key_exists('generateUserPDF', $this->config) && $this->config['generateUserPDF'] && $this->sendPDFToCustomer) {
            $customerPdf = $pdf;
        }

        // send admin mail if config exists
        if (array_key_exists('admin_mail', $this->config)) {
            $this->sendAdminMail($productInquiry, $customer, $adminPdf, $product);
        }

        // send customer mail if config exists
        if (array_key_exists('customer_mail', $this->config)) {
            $this->sendCustomerMail($productInquiry, $customer, $customerPdf, $product);
        }
    }

    /**
     * @param ProductInquiry $productInquiry
     * @param array $customer
     * @param string $pdf
     * @param array $product
     */
    protected function sendCustomerMail(ProductInquiry $productInquiry, array $customer, string $pdf, array $product)
    {
        $locale = new AptoLocale($productInquiry->getLocale());
        $hasBcc = array_key_exists('bcc', $this->config['customer_mail']);

        // set sender mail
        if ($this->partnerMail !== "" && $this->partnerName !== "") {
            $mailFrom = [
                'email' => $this->partnerMail,
                'name' => $this->partnerName
            ];
        } else {
            $mailFrom = [
                'email' => $this->config['customer_mail']['mail_from'],
                'name' => $this->config['customer_mail']['mail_from']
            ];
        }

        // get Template File
        if (array_key_exists('seperatePartnerMail', $this->config) && $this->config['seperatePartnerMail']){
            // specific Customer Mail for different Partners
            $templatePath = '@RequestForm/mail/customer/mail-'.$this->partnerID.'.html.twig';
        } else {
            $templatePath = '@RequestForm/mail/customer/mail.html.twig';
        }

        // create customer mail
        $mailCustomer = $this->generateMail(
            $this->config['customer_mail']['subject'],
            $mailFrom,
            [
                'email' => $customer['email'],
                'name' => $customer['name']
            ],
            $hasBcc ? $this->config['customer_mail']['bcc'] : [],
            $customer,
            $productInquiry->getState(),
            $templatePath,
            $productInquiry->getQuantity(),
            $productInquiry->getCompressedState(),
            $productInquiry->getRenderImages(),
            $locale,
            $pdf,
            $product,
            $this->hasCustomerElementAttachments()
        );

        // send customer mail
        $this->templateMailer->send($mailCustomer);
    }

    /**
     * @param ProductInquiry $productInquiry
     * @param array $customer
     * @param string $pdf
     * @param array $product
     */
    protected function sendAdminMail(ProductInquiry $productInquiry, array $customer, string $pdf, array $product)
    {
        $locale = new AptoLocale($productInquiry->getLocale());

        // set bcc
        $hasBcc = array_key_exists('bcc', $this->config['admin_mail']);

        // set recipient mail
        if ($this->partnerMail !== "" && $this->partnerName !== "") {
            $mailTo = [
                'email' => $this->partnerMail,
                'name' => $this->partnerName
            ];
        }
        else {
            $mailTo = [
                'email' => $this->config['admin_mail']['mail_to'],
                'name' => $this->config['admin_mail']['name_to']
            ];
        }

        // set sender mail
        $mailFrom = $mailTo;
        if (array_key_exists('mail_from', $this->config['admin_mail'])) {
            $mailFrom['email'] = $this->config['admin_mail']['mail_from'];
        }

        if (array_key_exists('name_from', $this->config['admin_mail'])) {
            $mailFrom['name'] = $this->config['admin_mail']['name_from'];
        }

        // create admin mail
        $mailAdmin = $this->generateMail(
            $this->config['admin_mail']['subject'],
            $mailFrom,
            $mailTo,
            $hasBcc ? $this->config['admin_mail']['bcc'] : [],
            $customer,
            $productInquiry->getState(),
            '@RequestForm/mail/admin/mail.html.twig',
            $productInquiry->getQuantity(),
            $productInquiry->getCompressedState(),
            $productInquiry->getRenderImages(),
            $locale,
            $pdf,
            $product,
            $this->hasAdminElementAttachments()
        );

        // create admin mail
        $this->templateMailer->send($mailAdmin);
    }

    /**
     * @param string $subject
     * @param array $emailFrom
     * @param array $emailTo
     * @param array $bcc
     * @param array $customer
     * @param array $state
     * @param string $template
     * @param int $quantity
     * @param array $compressedState
     * @param array $renderImages
     * @param string $locale
     * @param string $pdf
     * @param array $product
     * @param bool $addElementAttachments
     * @return array
     */
    protected function generateMail(
        string $subject,
        array $emailFrom,
        array $emailTo,
        array $bcc,
        array $customer,
        array $state,
        string $template,
        int $quantity,
        array $compressedState,
        array $renderImages,
        string $locale,
        string $pdf,
        array $product,
        bool $addElementAttachments
    ): array {
        $mail = [
            'subject' => $subject,
            'from' => [
                'email' => $emailFrom['email'],
                'name' => $emailFrom['name'] ?: ''
            ],
            'to' => [
                'email' => $emailTo['email'],
                'name' => $emailTo['name'] ?: ''
            ],
            'template' => $template,
            'context' => [
                'product' => $product,
                'customer' => $customer,
                'state' => $state,
                'locale' => $locale,
                'contentSnippets' => $this->contentSnippets['plugins']['requestForm']['mail'],
                'quantity' => $quantity,
                'pdfToCustomer' =>  $this->sendPDFToCustomer,
                'renderImages' => $renderImages,
                'compressedState' => $compressedState,
                'randomNumber' => $this->randomNumber,
                'configurationId' => $this->basketItem->getConfigurationId(),
                'prices' => $this->prices
            ],
            'attachments' => []
        ];

        if ($pdf !== '') {
            // set file name prefix
            $pdfFileNamePrefix = '';
            if (
                array_key_exists('fileNamePrefix', $this->contentSnippets['plugins']['requestForm']['pdf']) &&
                array_key_exists($locale, $this->contentSnippets['plugins']['requestForm']['pdf']['fileNamePrefix'])
            ) {
                $pdfFileNamePrefix = $this->contentSnippets['plugins']['requestForm']['pdf']['fileNamePrefix'][$locale];
            }

            // add pdf as mail attachment
            $mail['attachments'] = array_merge($mail['attachments'], [[
                'name' => $pdfFileNamePrefix . $this->randomNumber . '.pdf',
                'content' => $pdf,
                'contentType' => 'application/pdf'
            ]]);
        }

        if ($addElementAttachments === true) {
            $mail['attachments'] = array_merge($mail['attachments'], $this->elementAttachments);
        }

        foreach ($bcc as $value) {
            if ($value['email'] != '') {
                $mail['bcc'][] = [
                    'email' => $value['email'],
                    'name' => $value['name'] ?: ''
                ];
            }
        }

        return $mail;
    }

    /**
     * @return void
     */
    private function assertValidConfig()
    {
        if (array_key_exists('customer_mail', $this->config)) {
            if (
                // customer settings exists
                !array_key_exists('subject', $this->config['customer_mail']) ||
                !array_key_exists('mail_from', $this->config['customer_mail'])
            ) {
                throw new \InvalidArgumentException('Some settings are missing in parameters->apto_plugin_request_form');
            }
        }

        if (array_key_exists('admin_mail', $this->config)) {
            if (
                // admin settings exists
                !array_key_exists('subject', $this->config['admin_mail']) ||
                !array_key_exists('mail_to', $this->config['admin_mail'])
            ) {
                throw new \InvalidArgumentException('Some settings are missing in parameters->apto_plugin_request_form');
            }
        }
    }

    /**
     * @param array $product
     * @param array $formData
     * @param array $customer
     * @param ProductInquiry $productInquiry
     * @return false|string|void
     * @throws InvalidUuidException
     * @throws MpdfException
     */
    private function generatePDF(array $product, array $formData, array $customer, ProductInquiry $productInquiry)
    {
        // set locale
        $locale = new AptoLocale($productInquiry->getLocale());

        // get prices
        $prices = $this->prices;
        $sectionPrices = $prices['sections'];
        $sumPrices = $prices['sum'];

        $elementProperties = $this->getElementProperties($productInquiry->getState());
        $relevantElements = $this->getPDFVars($product['sections'], $productInquiry->getCompressedState(), $sectionPrices, $locale->getName(), false);
        $prioElements = $this->getPDFVars($product['sections'], $productInquiry->getCompressedState(), $sectionPrices, $locale->getName(), true);
        $productTitle = $this->getProductTitle($product['sections'], $productInquiry->getCompressedState(), $locale->getName());

        // Check for different PDFs for Partners
        if(array_key_exists('seperatePartnerPDF', $this->config) && $this->config['seperatePartnerPDF']){
            $pdfContentSnippets = $this->contentSnippets['plugins']['requestForm']['pdf'][$this->partnerID];
        } else {
            $pdfContentSnippets = $this->contentSnippets['plugins']['requestForm']['pdf'];
        }
        $pdfContentSnippets['tax'] = $this->contentSnippets['aptoSummary']['tax'];

        $mediaUrl = $this->requestSessionStore->getSchemeAndHttpHost() . $this->mediaRelativePath;
        $templateVars = [
            'locale' => $locale->getName(),
            'formData' => $formData,
            'customer' => $customer,
            'configuration' => $relevantElements,
            'prioConfiguration' => $prioElements,
            'elementProperties' => $elementProperties,
            'seperatePrioConfiguration' => $this->prioritySeperate,
            'onlyPrioConfiguration' => $this->priorityOnly,
            'products' => $productTitle,
            'mediaUrl' => $mediaUrl,
            'contentSnippets' => $pdfContentSnippets,
            'randomNumber' => $this->randomNumber,
            'sumPrices' => $sumPrices,
            'customerGroup' => $this->getCustomerGroup($productInquiry)
        ];

        // create pdf and set options
        $mpdf = new Mpdf([
            'default_font' => 'Verdana',
            'margin_top' => 40,
            'margin_bottom' => 30,
        ]);
        $mpdf->shrink_tables_to_fit = 1;

        // render stylesheet html
        $stylesheet = $this->templateRenderer->render('@RequestForm/pdf/style.css');

        // get the current page break margin.
        $mpdf->SetAutoPageBreak(true, 30);

        // set pdf stylesheet
        $mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);

        // render templates
        $header = $this->templateRenderer->render('@RequestForm/pdf/header.html.twig', $templateVars);
        $footer = $this->templateRenderer->render('@RequestForm/pdf/footer.html.twig', $templateVars);
        $body = $this->templateRenderer->render('@RequestForm/pdf/body.html.twig', $templateVars);

        // set pdf html
        $mpdf->SetHTMLHeader(
            $header
        );
        $mpdf->SetHTMLFooter(
            $footer
        );
        $mpdf->WriteHTML(
            $body
        );

        //Store HTML in Database
        if(array_key_exists('saveOfferHtml', $this->config) && $this->config['saveOfferHtml']){
            $this->saveHtml($header, $footer, $body);
        }

        return $mpdf->Output('', 'S');
    }

    /**
     * @param ProductInquiry $productInquiry
     * @return array
     */
    private function getCustomerGroup(ProductInquiry $productInquiry): array
    {
        $additionalData = $productInquiry->getAdditionalData();
        if (array_key_exists('customerGroup', $additionalData)) {
            return $additionalData['customerGroup'];
        }

        return [
            'id' => '00000000-0000-0000-0000-000000000000',
            'showGross' => true,
            'inputGross' => true,
            'fallback' => false
        ];
    }

    /**
     * @param $id
     * @param $array
     * @return bool
     */
    private function idInArray($id, $array): bool
    {
        foreach ($array as $key => $item) {
            if ($id === $key) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $humanReadableState
     * @return array
     */
    private function getElementProperties(array $humanReadableState): array
    {
        $elementProperties = [];

        foreach ($humanReadableState as $section) {
            foreach ($section as $element) {
                $elementProperties[$element['id']] = $element['values'];
            }
        }

        return $elementProperties;
    }

    /**
     * @param array $productSection
     * @param array $state
     * @param array $prices
     * @param string $locale
     * @param bool $prioData
     * @return array|null
     */
    private function getPDFVars(array $productSection, array $state, array $prices, string $locale, bool $prioData): ?array
    {
        $relevantData = [];
        $priorities = [];
        $hasPriorities = false;
        $hasPrioOnly = false;

        foreach ($productSection as $section) {
            $sectionId = $section['id'];

            if ($this->idInArray($sectionId, $state)) {
                foreach ($section['elements'] as $element) {
                    $append = true;
                    $isPrio = false;
                    $elementId = $element['id'];

                    if ($this->idInArray($elementId, $state[$section['id']])) {
                        $tempArray = [];

                        $tempArray['elementId'] = $elementId;
                        $tempArray['name'] = $element['name'];
                        $tempArray['description'] = $element['description'];
                        $tempArray['sectionName'] = $section['name'];

                        if (intval($prices[$sectionId]['elements'][$elementId]['own']['price']['amount']) === 0) {
                            $price = null;

                            foreach ($element['customProperties'] as $customProperty) {
                                if ($customProperty['key'] === 'pdfPrice') {
                                    $price = $customProperty['value'][$locale];
                                }
                                if ($customProperty['key'] === 'hideInPDF' || $customProperty['key'] === 'prioritySeperate') {
                                    $append = false;
                                }
                                if ($customProperty['key'] === 'sendPDFToCustomer') {
                                    $this->sendPDFToCustomer = false;
                                }
                                if($customProperty['key'] === 'priorityOnly' || $customProperty['key'] === 'prioritySeperate'){
                                    $hasPriorities = true;
                                    $isPrio = true;
                                }
                                if($customProperty['key'] === 'priorityOnly'){
                                    $hasPrioOnly = true;
                                    $this->priorityOnly = true;
                                }
                                if($customProperty['key'] === 'prioritySeperate'){
                                    $this->prioritySeperate = true;
                                }

                            }
                            $tempArray['price'] = $price;
                        } else {
                            $tempArray['price'] = $prices[$sectionId]['elements'][$elementId]['own']['price']['formatted'];
                        }
                        if ($append) {
                            array_push($relevantData, $tempArray);
                        }
                        if($isPrio){
                            array_push($priorities, $tempArray);
                        }
                    }
                }
            }
        }

        if ($hasPrioOnly) {
            return $priorities;
        }
        else if($prioData && $hasPriorities) {
            return $priorities;
        }
        /** @phpstan-ignore-next-line  */
        else if ($prioData && !$hasPriorities) {
            return null;
        }
        return $relevantData;
    }

    /**
     * @param array $productSection
     * @param array $state
     * @param string $locale
     * @return mixed|null
     */
    private function getProductTitle(array $productSection, array $state, string $locale)
    {
        $productTitle = null;
        foreach ($productSection as $section) {
            $sectionId = $section['id'];

            if ($this->idInArray($sectionId, $state)) {
                foreach ($section['elements'] as $element) {
                    $elementId = $element['id'];

                    if ($this->idInArray($elementId, $state[$section['id']])) {

                        foreach ($element['customProperties'] as $customProperty) {
                            if($customProperty['key'] === 'pdfProductTitle') {
                                $productTitle = $state[$section['id']][$element['id']]['text'];
                            }
                        }
                    }
                }
            }
            // Must not be an Selected Element
            foreach ($section['elements'] as $element) {
                $elementId = $element['id'];
                foreach ($element['customProperties'] as $customProperty) {
                    if($customProperty['key'] === 'customProductTitle') {
                        $productTitle = $customProperty['value'][$locale];
                    }
                }
            }
        }
        return $productTitle;
    }

    /**
     * @throws AllRandomNumbersHasUsedException
     * @throws InvalidUuidException
     */
    private function setRandomNumber()
    {
        $min_value = 0;
        $max_value = 100000;
        $max_attempts = $max_value - $min_value;
        $attempt = $min_value;

        while ($attempt < $max_attempts) {
            $number = $this->randomNumberPrefix . str_pad((string) mt_rand($min_value, $max_value), 8, '0', STR_PAD_LEFT);

            //isCodeUnique
            $item = $this->randomNumberRepository->findByNumber($number);

            if (!$item) {
                $this->randomNumber = $number;
                break;
            }

            $attempt++;
        }

        if (!$this->randomNumber) {
            throw new AllRandomNumbersHasUsedException('All random numbers has been used. In Plugin: apto-plugin-request-form.');
        }

        $randomNumber = new RandomNumber(
            $this->randomNumberRepository->nextIdentity(),
            $this->randomNumber
        );

        $this->randomNumberRepository->add($randomNumber);
        $randomNumber->publishEvents();
    }

    /**
     * @param string $header
     * @param string $footer
     * @param string $body
     * @throws InvalidUuidException
     */
    private function saveHtml(string $header, string $footer, string $body)
    {
        $offerHTML = new OfferHtml($this->offerHtmlRepository->nextIdentity(), $this->randomNumber, $header, $footer, $body);
        $this->offerHtmlRepository->add($offerHTML);
        $offerHTML->publishEvents();
    }

    /**
     * @param array $customer
     * @param array $state
     * @throws InvalidUuidException
     */
    private function storeData(array $customer, array $state)
    {
        $data = [
            'customer' => $customer,
            'state' => $state
        ];
        $data = serialize($data);
        $offerData = new OfferData($this->offerDataRepository->nextIdentity(), $this->randomNumber, $data);
        $this->offerDataRepository->add($offerData);
        $offerData->publishEvents();
    }

    /**
     * @return bool
     */
    private function hasAdminElementAttachments(): bool
    {
        if (
            array_key_exists('add_element_attachments', $this->config['admin_mail']) && $this->config['admin_mail']['add_element_attachments']
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function hasCustomerElementAttachments(): bool
    {
        if (
            array_key_exists('add_element_attachments', $this->config['customer_mail']) && $this->config['customer_mail']['add_element_attachments']
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield ConfigurationFinished::class => [
            'method' => 'onConfigurationFinished',
            'bus' => 'event_bus'
        ];
    }
}
