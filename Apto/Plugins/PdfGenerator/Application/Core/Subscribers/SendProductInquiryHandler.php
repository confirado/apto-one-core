<?php

namespace Apto\Plugins\PdfGenerator\Application\Core\Subscribers;

use Exception;
use Money\Currency;
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

use Apto\Plugins\PdfGenerator\Domain\Core\Model\OfferData\OfferData;
use Apto\Plugins\PdfGenerator\Domain\Core\Model\OfferData\OfferDataRepository;
use Apto\Plugins\PdfGenerator\Domain\Core\Model\OfferHtml\OfferHtml;
use Apto\Plugins\PdfGenerator\Domain\Core\Model\OfferHtml\OfferHtmlRepository;
use Apto\Plugins\PdfGenerator\Domain\Core\Model\RandomNumber\RandomNumberRepository;
use Apto\Plugins\PdfGenerator\Domain\Core\Model\RandomNumber\RandomNumber;
use Apto\Plugins\PdfGenerator\Domain\Core\Model\RandomNumber\AllRandomNumbersHasUsedException;
use Apto\Plugins\FileUpload\Domain\Core\Model\Service\Converter\MimeTypeExtensionConverter;
use Apto\Plugins\PartsList\Application\Core\Service\Converter\CsvStringConverter;
use Apto\Plugins\PdfGenerator\Application\Core\Service\Pdf\PdfFactory;
use Apto\Plugins\PdfGenerator\Application\Core\Service\Pdf\PdfTemplateVars;

class SendProductInquiryHandler implements EventHandlerInterface
{
    /**
     * @var RequestStore
     */
    protected RequestStore $requestSessionStore;

    /**
     * @var RandomNumberRepository
     */
    private RandomNumberRepository $randomNumberRepository;

    /**
     * @var OfferHtmlRepository
     */
    private OfferHtmlRepository $offerHtmlRepository;

    /**
     * @var OfferDataRepository
     */
    private OfferDataRepository $offerDataRepository;

    /**
     * @var ProductFinder
     */
    protected ProductFinder $productFinder;

    /**
     * @var ContentSnippetFinder
     */
    private ContentSnippetFinder $contentSnippetFinder;

    /**
     * @var StatePriceService
     */
    protected StatePriceService $statePriceService;

    /**
     * @var TemplateMailerInterface
     */
    protected TemplateMailerInterface $templateMailer;

    /**
     * @var TemplateRendererInterface
     */
    private TemplateRendererInterface $templateRenderer;

    /**
     * @var MediaFileSystemConnector
     */
    private MediaFileSystemConnector $mediaFileSystemConnector;

    /**
     * @var array|null
     */
    private ?array $config;

    /**
     * @var string
     */
    private string $mediaRelativePath;

    /**
     * @var string
     */
    private string $mediaDirectory;

    /**
     * @var bool
     */
    private bool $sendPDFToCustomer;

    /**
     * @var string|int
     */
    private $randomNumber;

    /**
     * @var mixed|string
     */
    private $randomNumberPrefix;

    /**
     * @var BasketItem|null
     */
    private ?BasketItem $basketItem;

    /**
     * @var array
     */
    private array $prices;

    /**
     * @var array
     */
    private array $elementAttachments;

    /**
     * @var array
     */
    private array $contentSnippets;

    /**
     * @var array|null
     */
    protected ?array $shop;

    /**
     * @var CsvStringConverter
     */
    private CsvStringConverter $csvStringConverter;

    /**
     * @var PdfTemplateVars
     */
    private PdfTemplateVars $pdfTemplateVars;

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
     * @param CsvStringConverter $csvStringConverter
     * @param PdfTemplateVars $pdfTemplateVars
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
        MediaFileSystemConnector $mediaFileSystemConnector,
        CsvStringConverter $csvStringConverter,
        PdfTemplateVars $pdfTemplateVars
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
        $this->csvStringConverter = $csvStringConverter;
        $this->contentSnippets = $this->contentSnippetFinder->getTree(true, $this->requestSessionStore->getHttpHost());
        $this->pdfTemplateVars = $pdfTemplateVars;

        // params
        $this->mediaRelativePath = $aptoParameter->get('media_relative_path');
        $this->mediaDirectory = $aptoParameter->get('media_directory');
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
            if ($customProperty['key'] === 'pdfGenerator' && $customProperty['value'] === 'disabled') {
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
        if (!$aptoParameter->has('apto_plugin_pdf_generator')) {
            $this->config = null;
            return;
        }

        $host = $requestSessionStore->getHttpHost();
        $config = $aptoParameter->get('apto_plugin_pdf_generator');
        $domainConfig = [];

        if (!array_key_exists('showPrices', $config)) {
            $config['showPrices'] = true;
        }

        if (array_key_exists('domain_config', $config) && array_key_exists($host, $config['domain_config'])) {
            $domainConfig = $config['domain_config'][$host];
        }

        if (array_key_exists('domain_config', $config)) {
            unset($config['domain_config']);
        }

        $this->config = array_replace_recursive($config, $domainConfig);
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

                // we don't need to consider repetitions here as attachments are all the same in all repetitions
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

        // generate pdf
        $pdf = '';
        $adminPdf = '';
        $customerPdf = '';
        $partsListCsv = '';

        if (
            array_key_exists('generateAdminPDF', $this->config) && $this->config['generateAdminPDF'] ||
            array_key_exists('generateUserPDF', $this->config) && $this->config['generateUserPDF']
        ) {
            try {
                $pdf = $this->generatePDF($product, $formData, $customer, $productInquiry);
            } catch (\Exception $e) {
                echo $e->getFile();
                echo $e->getLine();
                echo $e->getMessage();
            }
        }

        // set admin pdf
        if (array_key_exists('generateAdminPDF', $this->config) && $this->config['generateAdminPDF']) {
            $adminPdf = $pdf;
        }

        // set customer pdf
        if (array_key_exists('generateUserPDF', $this->config) && $this->config['generateUserPDF'] && $this->sendPDFToCustomer) {
            $customerPdf = $pdf;
        }

        // set parts list csv
        if ($this->hasAdminPartsListAttachment() || $this->hasCustomerPartsListAttachment() || $this->hasPartsListFtpUpload()) {
            $partsListCsv = $this->csvStringConverter->convert(
                new AptoUuid($productInquiry->getProductId()),
                new State($productInquiry->getCompressedState()),
                new Currency($productInquiry->getDisplayCurrency()['currency']),
                $this->shop['id'],
                $this->getCustomerGroup($productInquiry)['id']
            );
        }

        // send admin mail if config exists
        if (array_key_exists('admin_mail', $this->config)) {
            $this->sendAdminMail($productInquiry, $customer, $adminPdf, $product, $partsListCsv);
        }

        // send customer mail if config exists
        if (array_key_exists('customer_mail', $this->config)) {
            $this->sendCustomerMail($productInquiry, $customer, $customerPdf, $product, $partsListCsv);
        }

        // upload parts list
        if ($this->hasPartsListFtpUpload()) {
            $this->uploadPartsList($partsListCsv);
        }
    }

    /**
     * @param ProductInquiry $productInquiry
     * @param array $customer
     * @param string $pdf
     * @param array $product
     * @param $partsListCsv
     * @return void
     */
    protected function sendCustomerMail(ProductInquiry $productInquiry, array $customer, string $pdf, array $product, $partsListCsv)
    {
        $locale = new AptoLocale($productInquiry->getLocale());
        $hasBcc = array_key_exists('bcc', $this->config['customer_mail']);

        // set sender mail
        $mailFrom = [
            'email' => $this->config['customer_mail']['mail_from'],
            'name' => $this->config['customer_mail']['name_from']
        ];

        // get Template File
        $templatePath = '@PdfGenerator/mail/customer/mail.html.twig';

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

        // add parts list attachment
        if ($this->hasCustomerPartsListAttachment()) {
            $mailCustomer['attachments'][] = [
                'content' => $partsListCsv,
                'name' => 'stückliste.csv',
                'contentType' => 'text/csv'
            ];
        }

        // send customer mail
        $this->templateMailer->send($mailCustomer);
    }

    /**
     * @param ProductInquiry $productInquiry
     * @param array $customer
     * @param string $pdf
     * @param array $product
     * @param string $partsListCsv
     * @return void
     */
    protected function sendAdminMail(ProductInquiry $productInquiry, array $customer, string $pdf, array $product, string $partsListCsv)
    {
        $locale = new AptoLocale($productInquiry->getLocale());

        // set bcc
        $hasBcc = array_key_exists('bcc', $this->config['admin_mail']);

        // set recipient mail
        $mailTo = [
            'email' => $this->config['admin_mail']['mail_to'],
            'name' => $this->config['admin_mail']['name_to']
        ];

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
            '@PdfGenerator/mail/admin/mail.html.twig',
            $productInquiry->getQuantity(),
            $productInquiry->getCompressedState(),
            $productInquiry->getRenderImages(),
            $locale,
            $pdf,
            $product,
            $this->hasAdminElementAttachments()
        );

        // add parts list attachment
        if ($this->hasAdminPartsListAttachment()) {
            $mailAdmin['attachments'][] = [
                'content' => $partsListCsv,
                'name' => 'stückliste.csv',
                'contentType' => 'text/csv'
            ];
        }

        // create admin mail
        $this->templateMailer->send($mailAdmin);
    }

    /**
     * @param string $partsListCsv
     * @return void
     * @throws Exception
     */
    private function uploadPartsList(string $partsListCsv): void
    {
        $ftpConfig = $this->config['ftp_upload_parts_list'];
        $fileName = 'stueckliste_' . $this->basketItem->getConfigurationId() . '.csv';
        $stream = fopen('php://memory','r+');
        fwrite($stream, $partsListCsv);
        rewind($stream);

        // connect to ftp server
        $ftp = ftp_connect($ftpConfig['host'], $ftpConfig['port'] ?? 21, $ftpConfig['timeout'] ?? 10,);
        if (!$ftp) {
            fclose($stream);
            throw new Exception('Could not connect to ftp server. ErrorCode: "RF-FUPL-C".');
        }

        // login to ftp server
        $login = ftp_login($ftp, $ftpConfig['user'], $ftpConfig['password']);
        if (!$login) {
            fclose($stream);
            throw new Exception('Could not connect to ftp server. ErrorCode: "RF-FUPL-L".');
        }

        // upload to ftp server
        $success = ftp_fput($ftp, '/' . $ftpConfig['folder'] . '/' . $fileName, $stream, FTP_ASCII);
        if (!$success) {
            fclose($stream);
            throw new Exception('Could not connect to ftp server. ErrorCode: "RF-FUPL-U".');
        }

        fclose($stream);
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
                'contentSnippets' => $this->contentSnippets['plugins']['pdfGenerator']['mail'],
                'quantity' => $quantity,
                'pdfToCustomer' =>  $this->sendPDFToCustomer,
                'renderImages' => $renderImages,
                'compressedState' => $compressedState,
                'randomNumber' => $this->randomNumber,
                'configurationId' => $this->basketItem->getConfigurationId(),
                'prices' => $this->prices,
                'showPrices' => $this->config['showPrices']
            ],
            'attachments' => []
        ];

        if ($pdf !== '') {
            // set file name prefix
            $pdfFileNamePrefix = '';
            if (
                array_key_exists('fileNamePrefix', $this->contentSnippets['plugins']['pdfGenerator']['pdf']) &&
                array_key_exists($locale, $this->contentSnippets['plugins']['pdfGenerator']['pdf']['fileNamePrefix'])
            ) {
                $pdfFileNamePrefix = $this->contentSnippets['plugins']['pdfGenerator']['pdf']['fileNamePrefix'][$locale];
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
                throw new \InvalidArgumentException('Some settings are missing in parameters->apto_plugin_pdf_generator');
            }
        }

        if (array_key_exists('admin_mail', $this->config)) {
            if (
                // admin settings exists
                !array_key_exists('subject', $this->config['admin_mail']) ||
                !array_key_exists('mail_to', $this->config['admin_mail'])
            ) {
                throw new \InvalidArgumentException('Some settings are missing in parameters->apto_plugin_pdf_generator');
            }
        }
    }

    /**
     * @param array $product
     * @param array $formData
     * @param array $customer
     * @param ProductInquiry $productInquiry
     * @return string
     * @throws InvalidUuidException
     * @throws MpdfException
     */
    private function generatePDF(array $product, array $formData, array $customer, ProductInquiry $productInquiry): string
    {
        // set locale
        $locale = new AptoLocale($productInquiry->getLocale());

        // get prices
        $prices = $this->prices;
        $sectionPrices = $prices['sections'];
        $sumPrices = $prices['sum'];

        // set content snippets
        $pdfContentSnippets = $this->contentSnippets['plugins']['pdfGenerator']['pdf'];
        $pdfContentSnippets['tax'] = $this->contentSnippets['aptoSummary']['tax'];

        // set media url
        $mediaUrl = $this->requestSessionStore->getSchemeAndHttpHost() . $this->mediaRelativePath;

        $customerGroup = $this->getCustomerGroup($productInquiry);
        $partsList = isset($productInquiry->getAdditionalData()['apto-plugin-parts-list']) ?
            $productInquiry->getAdditionalData()['apto-plugin-parts-list'][$customerGroup['id']] : [];

        // set template vars
        $this->pdfTemplateVars->setTemplateVars([
            'locale' => $locale->getName(),
            'formData' => $formData,
            'customer' => $customer,
            'product' => $product,
            'configurationId' => $this->basketItem->getConfigurationId(),
            'sortedProperties' => $this->getTranslatedSortedProperties($locale),
            'sectionPrices' => $sectionPrices,
            'mediaUrl' => $mediaUrl,
            'contentSnippets' => $pdfContentSnippets,
            'randomNumber' => $this->randomNumber,
            'sumPrices' => $sumPrices,
            'customerGroup' => $customerGroup,
            'showPrices' => $this->config['showPrices'],
            'partsList' => $partsList,
            'prices' => $prices
        ]);

        // create pdf and set options
        $mpdf = PdfFactory::create();

        // render stylesheet html
        $stylesheet = $this->templateRenderer->render('@PdfGenerator/pdf/style.css');

        // set pdf stylesheet
        $mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);

        // render templates
        $header = $this->templateRenderer->render('@PdfGenerator/pdf/header.html.twig', $this->pdfTemplateVars->getTemplateVars($productInquiry));
        $footer = $this->templateRenderer->render('@PdfGenerator/pdf/footer.html.twig', $this->pdfTemplateVars->getTemplateVars($productInquiry));
        $body = $this->templateRenderer->render('@PdfGenerator/pdf/body.html.twig', $this->pdfTemplateVars->getTemplateVars($productInquiry));

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

        // for testing only!!!, saves the pdf into media folder as pdf and as html
        // $this->saveHTMLToDisk($stylesheet, $header, $body, $footer);
        // $this->savePdfToDisk($mpdf);

        return $mpdf->Output('', 'S');
    }

    /**
     *  Save as pdf file in media folder
     *
     * @param $mpdf
     *
     * @return void
     * @throws \Random\RandomException
     */
    private function savePdfToDisk($mpdf): void
    {
        $mpdf->Output( $this->mediaDirectory . '/'. substr(bin2hex(random_bytes(5)), 0, 10) . '.pdf', 'F');
    }

    /**
     *  Save the content of pdf as html file
     *
     *  For testing only!!!
     *
     * @param $stylesheet
     * @param $header
     * @param $body
     * @param $footer
     *
     * @return void
     * @throws \Random\RandomException
     */
    private function saveHTMLToDisk($stylesheet, $header, $body, $footer): void
    {
        $filename = $this->mediaDirectory . '/'. substr(bin2hex(random_bytes(5)), 0, 10) . '.html';
        $stylesheet = str_replace('@media print {', '@media print, all {', $stylesheet);
        $content = '<html><body>' .'<style>' . $stylesheet . '</style>'. $header . $body . $footer  . '</body></html>';

        file_put_contents($filename, $content);
    }

    /**
     * @param AptoLocale $locale
     * @return array
     */
    private function getTranslatedSortedProperties(AptoLocale $locale): array
    {
        $additionalData = $this->basketItem->getAdditionalData();

        // return empty array if sorted properties not in data
        if (
            !array_key_exists('translations', $additionalData) ||
            !array_key_exists('root', $additionalData['translations']) ||
            !array_key_exists('sortedProperties', $additionalData['translations']['root'])
        ) {
            return [];
        }

        // return for current local
        $sortedProperties = $additionalData['translations']['root']['sortedProperties'];
        if (array_key_exists($locale->getName(), $sortedProperties)) {
            return $sortedProperties[$locale->getName()];
        }

        // search for fallback de_DE
        $locals = array_keys($sortedProperties);
        if (in_array('de_DE', $locals)) {
            return $sortedProperties['de_DE'];
        }

        // search for first translation
        if (count($locals) > 0) {
            return $sortedProperties[$locals[0]];
        }

        // return empty array if no translation is available
        return [];
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
    private function hasAdminPartsListAttachment(): bool
    {
        if (
            array_key_exists('add_parts_list_attachment', $this->config['admin_mail']) && $this->config['admin_mail']['add_parts_list_attachment']
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
     * @return bool
     */
    private function hasCustomerPartsListAttachment(): bool
    {
        if (
            array_key_exists('add_parts_list_attachment', $this->config['customer_mail']) && $this->config['customer_mail']['add_parts_list_attachment']
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function hasPartsListFtpUpload(): bool
    {
        if (
            array_key_exists('ftp_upload_parts_list', $this->config) && array_key_exists('active', $this->config['ftp_upload_parts_list']) && $this->config['ftp_upload_parts_list']['active']
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
