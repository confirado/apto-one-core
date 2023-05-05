<?php
namespace Apto\Catalog\Application\Frontend\Subscribers\Configuration;

use Apto\Base\Application\Core\EventHandlerInterface;
use Apto\Base\Application\Core\Query\ContentSnippet\ContentSnippetFinder;
use Apto\Base\Application\Core\Service\TemplateMailerInterface;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Application\Frontend\Events\Configuration\GuestConfigurationAdded;
use Apto\Catalog\Application\Frontend\Events\Configuration\OfferConfigurationAdded;
use Apto\Catalog\Domain\Core\Model\Shop\Shop;
use Apto\Catalog\Domain\Core\Model\Shop\ShopRepository;

class ConfigurationEventHandler implements EventHandlerInterface
{
    /**
     * @var TemplateMailerInterface
     */
    private $templateMailer;

    /**
     * @var RequestStore
     */
    protected $requestStore;

    /**
     * @var ShopRepository
     */
    protected $shopRepository;

    /**
     * @var Shop|null
     */
    protected $shop;

    /**
     * @var AptoLocale
     */
    protected $locale;

    /**
     * @var ContentSnippetFinder
     */
    protected $contentSnippetFinder;

    /**
     * @param TemplateMailerInterface $templateMailer
     * @param RequestStore $requestStore
     * @param ShopRepository $shopRepository
     * @param ContentSnippetFinder $contentSnippetFinder
     */
    public function __construct(
        TemplateMailerInterface $templateMailer,
        RequestStore $requestStore,
        ShopRepository $shopRepository,
        ContentSnippetFinder $contentSnippetFinder
    ) {
        $this->templateMailer = $templateMailer;
        $this->requestStore = $requestStore;
        $this->shopRepository = $shopRepository;
        $this->locale = new AptoLocale($this->requestStore->getLocale());
        $this->shop = $this->shopRepository->findOneByDomain($this->requestStore->getHttpHost());
        $this->contentSnippetFinder = $contentSnippetFinder;
    }

    /**
     * @param OfferConfigurationAdded $event
     * @return void
     */
    public function onOfferConfigurationAdded(OfferConfigurationAdded $event)
    {
        // if configuration id or customer email is not set we have nothing to do
        if (
            !$event->getConfigurationId() ||
            !$event->getCustomerEmail() ||
            !$this->shop->getOperatorEmail()->getEmail()
        ) {
            return;
        }

        // get content snippets
        $contentSnippets = $this->getOfferContentSnippets($this->locale->getName());

        // set bcc from content snippet
        $bccCS = AptoTranslatedValue::fromArray($contentSnippets['form']['bcc']);
        $bccCS = $bccCS->getTranslation(
            $this->locale,
            new AptoLocale('de_DE'),
            true
        )->getValue();

        $bccAddresses = explode(',', $bccCS);
        $bcc = [];
        if ($bccCS && count($bccAddresses) > 0) {
            foreach ($bccAddresses as $bccAddress) {
                $bcc[] = [
                    'email' => trim($bccAddress),
                    'name' => ''
                ];
            }
        }

        // create customer mail
        $mail = $this->generateMail(
            $this->getMailTemplate('offer'),
            AptoTranslatedValue::fromArray($contentSnippets['mail']['subject'])->getTranslation($this->locale, new AptoLocale('de_DE'))->getValue(),
            [
                'email' => $this->shop->getOperatorEmail()->getEmail(),
                'name' => $this->shop->getOperatorName()
            ],
            [
                'email' => $event->getCustomerEmail(),
                'name' => $event->getCustomerName()
            ],
            [
                'customer' => [
                    'email' => $event->getCustomerEmail(),
                    'name' => $event->getCustomerName(),
                ],
                'formData' => $event->getPayload()['formData'] ?? [],
                'form' => $contentSnippets['form'],
                'mail' => $contentSnippets['mail'],
                'configurationId' => $event->getConfigurationId(),
                'locale' => $this->locale->getName(),
                'payload' => $event->getPayload()
            ],
            $bcc
        );

        // send customer mail
        $this->templateMailer->send($mail);
    }

    /**
     * @param GuestConfigurationAdded $event
     * @return void
     */
    public function onGuestConfigurationAdded(GuestConfigurationAdded $event)
    {
        // if configuration id or customer email is not set we have nothing to do
        if (
            !$event->getConfigurationId() ||
            !$event->getCustomerEmail() ||
            !$this->shop->getOperatorEmail()->getEmail()
        ) {
            return;
        }

        // get content snippets
        $contentSnippets = $this->getGuestContentSnippets($this->locale->getName());

        // create customer mail
        $mail = $this->generateMail(
            $this->getMailTemplate('guest'),
            AptoTranslatedValue::fromArray($contentSnippets['mail']['subject'])->getTranslation($this->locale, new AptoLocale('de_DE'))->getValue(),
            [
                'email' => $this->shop->getOperatorEmail()->getEmail(),
                'name' => $this->shop->getOperatorName()
            ],
            [
                'email' => $event->getCustomerEmail(),
                'name' => $event->getCustomerName()
            ],
            [
                'customer' => [
                    'email' => $event->getCustomerEmail(),
                    'name' => $event->getCustomerName(),
                ],
                'formData' => $event->getPayload()['formData'] ?? [],
                'form' => $contentSnippets['form'],
                'mail' => $contentSnippets['mail'],
                'configurationId' => $event->getConfigurationId(),
                'locale' => $this->locale->getName(),
                'payload' => $event->getPayload()
            ],
            []
        );

        // send customer mail
        $this->templateMailer->send($mail);
    }

    /**
     * @param string $template
     * @param string $subject
     * @param array $emailFrom
     * @param array $emailTo
     * @param array $context
     * @param array $bcc
     * @return array
     */
    protected function generateMail(string $template, string $subject, array $emailFrom, array $emailTo, array $context, array $bcc): array
    {
        $mail = [];
        $mail['subject'] = $subject;

        $mail['from'] = [
            'email' => $emailFrom['email'],
            'name' => $emailFrom['name'] ?: ''
        ];

        $mail['to'] = [
            'email' => $emailTo['email'],
            'name' => $emailTo['name'] ?: ''
        ];

        $mail['template'] = $template;
        $mail['context'] = $context;
        $mail['bcc'] = $bcc;

        return $mail;
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getMailTemplate(string $type): string
    {
        $shopTemplateId = $this->getShopTemplateId();
        return '@AptoCatalog/' . $shopTemplateId . '/catalog/mail/guest-configuration/add-' . $type . '-configuration.html.twig';
    }

    /**
     * @return string
     */
    protected function getShopTemplateId(): string
    {
        if (null !== $this->shop && $this->shop->getTemplateId()) {
            return $this->shop->getTemplateId();
        }

        return 'apto';
    }

    /**
     * @param string $locale
     * @return array
     */
    public function getGuestContentSnippets(string $locale): array
    {
        $contentSnippets = [];
        $contentSnippetTree = $this->contentSnippetFinder->getTree(true, $this->requestStore->getHttpHost());

        if (
            array_key_exists('AptoGuestConfigurationDialog', $contentSnippetTree)
        ) {
            $contentSnippets = $contentSnippetTree['AptoGuestConfigurationDialog'];
        }

        if (!array_key_exists('mail', $contentSnippets)) {
            $contentSnippets['mail'] = [];
        }

        if (!array_key_exists('name', $contentSnippets)) {
            $contentSnippets['name'] = [];
        }

        if (!array_key_exists('form', $contentSnippets)) {
            $contentSnippets['form'] = [];
        }

        return $contentSnippets;
    }

    /**
     * @param string $locale
     * @return array
     */
    public function getOfferContentSnippets(string $locale): array
    {
        $contentSnippets = [];
        $contentSnippetTree = $this->contentSnippetFinder->getTree(true, $this->requestStore->getHttpHost());

        if (
            array_key_exists('AptoOfferConfigurationDialog', $contentSnippetTree)
        ) {
            $contentSnippets = $contentSnippetTree['AptoOfferConfigurationDialog'];
        }

        if (!array_key_exists('mail', $contentSnippets)) {
            $contentSnippets['mail'] = [];
        }

        if (!array_key_exists('name', $contentSnippets)) {
            $contentSnippets['name'] = [];
        }

        if (!array_key_exists('form', $contentSnippets)) {
            $contentSnippets['form'] = [];
        }

        if (!array_key_exists('fields', $contentSnippets['form'])) {
            $contentSnippets['form']['fields'] = [];
        }

        if (!array_key_exists('bcc', $contentSnippets['form'])) {
            $contentSnippets['form']['bcc'] = [];
        }

        $this->sortFormFields($contentSnippets['form']['fields'], $locale);

        return $contentSnippets;
    }

    /**
     * @param array $fields
     * @param string $locale
     */
    protected function sortFormFields(array &$fields, string $locale)
    {
        uasort($fields, function ($a, $b) use ($locale) {
            // check for undefined position
            $aIsUndefined = false;
            $bIsUndefined = false;

            if (!array_key_exists('position', $a) || !array_key_exists($locale, $a['position'])) {
                $aIsUndefined = true;
            }

            if (!array_key_exists('position', $b) || !array_key_exists($locale, $b['position'])) {
                $bIsUndefined = true;
            }

            // move fields with no position field or no translation for the current language to the end of the form
            if ($aIsUndefined && $bIsUndefined) {
                return 0;
            }

            if ($aIsUndefined) {
                return 1;
            }

            if ($bIsUndefined) {
                return -1;
            }

            // sort by value
            $a = intval($a['position'][$locale]);
            $b = intval($b['position'][$locale]);

            if ($a < $b) {
                return -1;
            }

            if ($a > $b) {
                return 1;
            }

            return 0;
        });
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield GuestConfigurationAdded::class => [
            'method' => 'onGuestConfigurationAdded',
            'bus' => 'event_bus'
        ];

        yield OfferConfigurationAdded::class => [
            'method' => 'onOfferConfigurationAdded',
            'bus' => 'event_bus'
        ];
    }
}
