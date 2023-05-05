<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\EventListener;

use Apto\Base\Application\Core\Query\Language\LanguageFinder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var LanguageFinder
     */
    private $languageFinder;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @param LanguageFinder $languageFinder
     * @param string $defaultLocale
     */
    public function __construct(LanguageFinder $languageFinder, string $defaultLocale = 'de_DE')
    {
        $this->languageFinder = $languageFinder;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param RequestEvent $event
     * @return void
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $locale = $request->attributes->get('_locale');
        $data = json_decode($request->getContent(), true);

        if (array_key_exists('_locale', $data ?? [])) {
            $locale = $data['_locale'];
        }

        // try to see if the locale has been set as a _locale routing parameter
        if ($locale && $this->isValidLocale($locale)) {
            $request->getSession()->set('_locale', $locale);
        }

        // if no explicit locale has been set on this request, use one from the session
        $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            // must be registered after the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 20)),
        ];
    }

    /**
     * @param string $locale
     * @return bool
     */
    private function isValidLocale(string $locale): bool
    {
        $languages = $this->languageFinder->findLanguages();
        foreach ($languages['data'] as $language) {
            if ($language['isocode'] === $locale) {
                return true;
            }
        }
        return false;
    }
}

