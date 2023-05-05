<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Template;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use Apto\Base\Application\Core\Service\TemplateRendererInterface;

class TemplateRenderer implements TemplateRendererInterface
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param string $template
     * @param array $context
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(string $template, array $context = []): string
    {
        return $this->twig->render($template, $context);
    }

    /**
     * @param string $template
     * @return bool
     */
    public function templateExists(string $template): bool
    {
        return $this->twig->getLoader()->exists($template);
    }
}