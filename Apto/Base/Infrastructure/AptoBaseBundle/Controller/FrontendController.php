<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller;

use Apto\Base\Domain\Core\Service\AptoParameterInterface;
use Throwable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Apto\Base\Application\Core\Query\Language\FindLanguages;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryBus;
use Apto\Base\Infrastructure\AptoBaseBundle\Template\TemplateLoader;
use Apto\Catalog\Application\Core\Query\Shop\FindShopContext;

class FrontendController extends AbstractController
{
    /**
     * @var QueryBus
     */
    private QueryBus $queryBus;

    /**
     * @var TemplateLoader
     */
    private TemplateLoader $templateLoader;

    /**
     * @var bool
     */
    private bool $installer;

    /**
     * @param QueryBus $queryBus
     * @param TemplateLoader $templateLoader
     * @param AptoParameterInterface $aptoParameter
     */
    public function __construct(QueryBus $queryBus, TemplateLoader $templateLoader, AptoParameterInterface $aptoParameter)
    {
        $this->queryBus = $queryBus;
        $this->templateLoader = $templateLoader;
        $this->installer = $aptoParameter->get('apto_installer') === 'enabled';
    }

    /**
     * @Route("/")
     * @Route("/{_locale}/")
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function indexAction(Request $request): Response
    {
        if ($this->installer === true) {
            return $this->redirectToRoute('apto_base_infrastructure_aptobase_install_aptoinstall');
        }

        return $this->renderIndex($request);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    protected function renderIndex(Request $request): Response
    {
        // get Shop
        $shop = $this->getShop($request->getHttpHost());
        $templateId = $shop['templateId'] ? $shop['templateId'] : 'apto';

        // get template data
        $templateLoaderData = $this->templateLoader->getData('frontend', $templateId);

        // get locale
        $locale = $request->getLocale();

        // get languages
        $languages = $this->getLanguages();

        // assign template vars
        $templateVars = [
            'metaTitle' => $this->getMetaTitle($request, $shop),
            'metaDescription' => $this->getMetaDescription($shop),
            'locale' => $locale,
            'languages' => $languages,
            'templateLoaderData' => $templateLoaderData,
            'perspectives' => $this->getParameter('perspectives'),
            'shopTemplate' => 'shop-template-' . $templateId,
            'aptoApi' => $this->templateLoader->getApiData('frontend', $templateId)
        ];

        // render template
        return $this->render('@AptoBase/apto/base/frontend/index.html.twig', $templateVars);
    }

    /**
     * @return array
     * @throws Throwable
     */
    protected function getLanguages(): array
    {
        $languages = [];
        $languagesQuery = new FindLanguages();
        $this->queryBus->handle($languagesQuery, $languages);

        if (array_key_exists('data', $languages)) {
            return $languages['data'];
        }

        return [];
    }

    /**
     * @param string $domain
     * @return array|null
     * @throws Throwable
     */
    protected function getShop(string $domain): ?array
    {
        $shop = null;
        $shopsQuery = new FindShopContext($domain);
        $this->queryBus->handle($shopsQuery, $shop);

        return $shop;
    }

    /**
     * @param Request $request
     * @param array $shop
     * @return string
     */
    protected function getMetaTitle(Request $request, array $shop): string
    {
        if (array_key_exists('name', $shop)) {
            if (!$shop['name']) {
                return '';
            }
            return $shop['name'];
        }
        return $request->getHttpHost() . ' - ' . 'Produktkonfigurator';
    }

    /**
     * @param array $shop
     * @return string
     */
    protected function getMetaDescription(array $shop): string
    {
        if (array_key_exists('description', $shop)) {
            if (!$shop['description']) {
                return '';
            }
            return $shop['description'];
        }
        return '';
    }
}
