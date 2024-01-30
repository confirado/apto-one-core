<?php

namespace Apto\Base\Domain\Frontend\Subscribers;

use Apto\Base\Application\Core\EventHandlerInterface;
use Apto\Base\Application\Core\Query\ContentSnippet\ContentSnippetFinder;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Application\Core\Service\TemplateMailerInterface;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Frontend\Events\ResetPasswordTokenCreated;
use Apto\Catalog\Domain\Core\Model\Shop\Shop;
use Apto\Catalog\Domain\Core\Model\Shop\ShopRepository;

class ResetPasswordTokenCreatedHandler implements EventHandlerInterface
{
    private TemplateMailerInterface $templateMailer;
    private RequestStore $requestStore;
    private AptoLocale $locale;
    private Shop $shop;
    private ContentSnippetFinder $contentSnippetFinder;

    public function __construct(
        TemplateMailerInterface $templateMailer,
        RequestStore $requestStore,
        ShopRepository $shopRepository,
        ContentSnippetFinder $contentSnippetFinder
    )
    {
        $this->templateMailer = $templateMailer;
        $this->requestStore = $requestStore;
        $this->locale = new AptoLocale($this->requestStore->getLocale());
        $this->shop = $shopRepository->findOneByDomain($this->requestStore->getHttpHost());
        $this->contentSnippetFinder = $contentSnippetFinder;
    }

    public function onTokenCreated(ResetPasswordTokenCreated $passwordTokenCreated)
    {
        $contentSnippetTree = $this->contentSnippetFinder->getTree(true, $this->requestStore->getHttpHost());
        $this->templateMailer->send([
            'subject' => AptoTranslatedValue::fromArray($contentSnippetTree['auth']['mail']['subject'])->getTranslation($this->locale)->getValue(),
            'from' => [
                'email' => $this->shop->getOperatorEmail()->getEmail(),
                'name' => $this->shop->getOperatorName()
            ],
            'to' => [
                'email' => $passwordTokenCreated->getEmail(),
                'name' => ''
            ],
            'template' => $this->getMailTemplate(),
            'context' => [
                'link' => '#/?action=updatePassword&token=' . $passwordTokenCreated->getToken(),
                'description' => AptoTranslatedValue::fromArray($contentSnippetTree['auth']['mail']['description'])->getTranslation($this->locale)->getValue(),
            ],
        ]);
    }

    public static function getHandledMessages(): iterable
    {
        yield ResetPasswordTokenCreated::class => [
            'method' => 'onTokenCreated',
            'bus' => 'event_bus'
        ];
    }

    private function getMailTemplate(): string
    {
        return '@AptoBase/apto/base/mail/reset-password.html.twig';
    }
}
