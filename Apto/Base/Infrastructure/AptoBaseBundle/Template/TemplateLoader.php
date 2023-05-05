<?php
namespace Apto\Base\Infrastructure\AptoBaseBundle\Template;

use SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;

class TemplateLoader
{
    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    /**
     * @var KernelInterface
     */
    private KernelInterface $kernel;

    /**
     * @var RequestStore
     */
    private RequestStore $requestStore;

    /**
     * @var AptoParameterInterface
     */
    private AptoParameterInterface $aptoParameter;

    /**
     * @var array
     */
    private array $data = [];

    /**
     * @var string
     */
    private string $clientEnvironment;

    /**
     * @param RouterInterface $router
     * @param KernelInterface $kernel
     * @param RequestStore $requestStore
     * @param AptoParameterInterface $aptoParameter
     */
    public function __construct(
        RouterInterface $router,
        KernelInterface $kernel,
        RequestStore $requestStore,
        AptoParameterInterface $aptoParameter
    ) {
        $this->router = $router;
        $this->kernel = $kernel;
        $this->requestStore = $requestStore;
        $this->aptoParameter = $aptoParameter;

        $data = [
            'templates' => [
                'angularTemplates' => []
            ],
            'routes' => [
                'angular' => [],
                'routeNames' => [],
                'routeUrls' => []
            ],
            'mainMenuEntries' => []
        ];

        $this->data['backend'] = $data;
        $this->data['frontend'] = $data;
        $this->data['install'] = $data;

        $this->clientEnvironment = trim($aptoParameter->get('client_environment'));
    }

    /**
     * @param BackendTemplateInterface $backendTemplate
     */
    public function addBackendTemplate(BackendTemplateInterface $backendTemplate)
    {
        $routes = $backendTemplate->getRoutes();
        $templates = $backendTemplate->getTemplates();
        $mainMenuEntries = $backendTemplate->getMainMenuEntries();

        $this->addRoutes($routes[0], $routes[1]);
        $this->addTemplates($templates[0], $templates[1]);
        $this->addMainMenuEntries($mainMenuEntries[0], $mainMenuEntries[1]);
    }

    /**
     * $context can be "backend" or "frontend"
     * @param array $data
     * @param string $context
     */
    public function addTemplates(array $data, string $context)
    {
        // @todo we should use array_replace_recursive() here, but first we must make sure every node has a non numeric index
        // see https://jontai.me/blog/2011/12/array_merge_recursive-vs-array_replace_recursive/ for details
        $this->data[$context]['templates'] = array_merge_recursive($this->data[$context]['templates'], $data);
    }

    /**
     * $context can be "backend" or "frontend"
     * @param array $data
     * @param string $context
     */
    public function addRoutes(array $data, string $context)
    {
        // @todo we should use array_replace_recursive() here, but first we must make sure every node has a non numeric index
        // see https://jontai.me/blog/2011/12/array_merge_recursive-vs-array_replace_recursive/ for details
        $this->data[$context]['routes'] = array_merge_recursive($this->data[$context]['routes'], $data);
    }

    /**
     * $context can be "backend" or "frontend"
     * @param array $data
     * @param string $context
     */
    public function addMainMenuEntries(array $data, string $context)
    {
        // @todo we should use array_replace_recursive() here, but first we must make sure every node has a non numeric index
        // see https://jontai.me/blog/2011/12/array_merge_recursive-vs-array_replace_recursive/ for details
        $this->data[$context]['mainMenuEntries'] = array_merge_recursive($this->data[$context]['mainMenuEntries'], $data);
    }

    /**
     * $context can be "backend" or "frontend"
     * @param string $context
     * @param string $template
     * @return array
     */
    public function getData(string $context, string $template = 'apto'): array
    {
        $this->processApiRoutes($context);
        $this->data[$context]['webpackFiles'] = $this->getWebpackFiles($context, $template);
        $this->data[$context]['customFiles'] = $this->getCustomFiles();

        return $this->data[$context];
    }

    /**
     * @param string $context
     * @param string $template
     *
     * @return string[]
     */
    public function getApiData(string $context = 'frontend', string $template = 'apto'): array
    {
        // set root url
        $publicFolder = $this->getPublicFolder();
        $rootUrl = $this->requestStore->getSchemeAndHttpHost() . $publicFolder;

        // set client folder
        $env = $this->kernel->getEnvironment();
        $client = '/public/dist/' . $context . '/' . $env . '/' . $template;
        $clientEnvironment = trim($this->clientEnvironment);
        if ($clientEnvironment) {
            $client .= '/' . $clientEnvironment;
        }

        if ($context === 'backend') {
            $api = [
                'root' => $rootUrl,
                'query' => $rootUrl . '/backend/message-bus/query',
                'command' => $rootUrl . '/backend/message-bus/command',
                'batchExecute' => $rootUrl . '/backend/message-bus/batch-execute',
                'setLocale' => $rootUrl . '/backend/message-bus/setLocale',
                'thumb' => $rootUrl . '/public/thumbs',
                'media' => $rootUrl . '/public/media',
                'client' => $rootUrl . $client,
                'versions' => $this->getVersions()
            ];
        } else {
            $api = [
                'root' => $rootUrl,
                'query' => $rootUrl . '/message-bus/query',
                'command' => $rootUrl . '/message-bus/command',
                'batchExecute' => $rootUrl . '/message-bus/batch-execute',
                'setLocale' => $rootUrl . '/message-bus/setLocale',
                'thumb' => $rootUrl . '/public/thumbs',
                'media' => $rootUrl . '/public/media',
                'client' => $rootUrl . $client
            ];
        }

        return $api;
    }

    /**
     * $context can be "backend" or "frontend"
     * @param string $context
     */
    protected function processApiRoutes(string $context)
    {
        $mediaUrl = $this->requestStore->getSchemeAndHttpHost()  . $this->getMediaRelativePath();

        $this->data[$context]['routes']['routeUrls']['media_url'] = $mediaUrl;


        foreach ($this->data[$context]['routes']['routeNames'] as $key => $apiRoute) {
            $this->data[$context]['routes']['routeUrls'][$key] = $this->generateUrl($apiRoute, [], UrlGeneratorInterface::ABSOLUTE_URL);
        }
    }

    /**
     * @param string $route
     * @param array $parameters
     * @param int $referenceType
     * @return string
     */
    protected function generateUrl(string $route, array $parameters, int $referenceType): string
    {
        return $this->router->generate($route, $parameters, $referenceType);
    }

    /**
     * @param string $context
     * @param string $template
     * @return array
     */
    protected function getWebpackFiles(string $context, string $template = 'apto'): array
    {
        // set env variables
        $env = $this->kernel->getEnvironment();
        $clientEnvironment = trim($this->clientEnvironment);
        if ($clientEnvironment) {
            $clientEnvironment .= '/';
        } else {
            $clientEnvironment = '';
        }

        $searchPath = 'public/dist/' . $context . '/' . $env . '/' . $template . '/' . $clientEnvironment;
        $absolutePath = realpath($this->kernel->getProjectDir() . '/web/' . $searchPath);
        $allFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($absolutePath));

        $jsFilesIterator = new \RegexIterator($allFiles, '/\.js$/');
        $cssFilesIterator = new \RegexIterator($allFiles, '/\.css$/');

        $files = [
            'js' => [],
            'css' => []
        ];

        // @todo find a better approach to keep angular files in the right order
        $angularJsFiles = [
            'runtime' => null,
            'polyfills' => null,
            'main' => null
        ];

        /** @var SplFileInfo $jsFile */
        foreach ($jsFilesIterator as $jsFile) {
            if (strpos($jsFile->getFilename(), 'runtime') !== false) {
                $angularJsFiles['runtime'] = $this->getRelPath($jsFile->getRealPath(), $searchPath);
                continue;
            }

            if (strpos($jsFile->getFilename(), 'polyfills') !== false) {
                $angularJsFiles['polyfills'] = $this->getRelPath($jsFile->getRealPath(), $searchPath);
                continue;
            }

            if (strpos($jsFile->getFilename(), 'main') !== false) {
                $angularJsFiles['main'] = $this->getRelPath($jsFile->getRealPath(), $searchPath);
                continue;
            }

            $files['js'][] = $this->getRelPath($jsFile->getRealPath(), $searchPath);
        }

        if ($angularJsFiles['main']) {
            array_unshift($files['js'], $angularJsFiles['main']);
        }

        if ($angularJsFiles['polyfills']) {
            array_unshift($files['js'], $angularJsFiles['polyfills']);
        }

        if ($angularJsFiles['runtime']) {
            array_unshift($files['js'], $angularJsFiles['runtime']);
        }

        foreach ($cssFilesIterator as $cssFile) {
            $files['css'][] = $this->getRelPath($cssFile->getRealPath(), $searchPath);
        }

        return $files;
    }

    /**
     * @param string $path
     * @param string $searchPath
     * @return bool|string
     */
    protected function getRelPath(string $path, string $searchPath)
    {
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        $rootPos = strpos($path, $searchPath);

        if($rootPos === false){
            return false;
        }

        return substr($path, $rootPos);
    }

    /**
     * @return array
     */
    protected function getCustomFiles(): array
    {
        // init customFiles
        $customFiles = [
            'js' => [],
            'css' => []
        ];

        // generate CSS file and path
        $customCssPath = 'public/assets/css';
        $customCssMediaPath = 'public/media/apto/css';
        $customCssFile = '/custom.css';
        $customCssSearchPath = realpath($this->kernel->getProjectDir() . '/web/' . $customCssPath);
        $customCssMediaSearchPath = realpath($this->kernel->getProjectDir() . '/web/' . $customCssMediaPath);

        // generate JS file and path
        $customJsPath = 'public/assets/js';
        $customJsFile = '/custom.js';
        $customJsSearchPath = realpath($this->kernel->getProjectDir() . '/web/' . $customJsPath);

        // check if customer.js file exist and add to customFiles
        if (file_exists($customJsSearchPath.$customJsFile)) {
            //@ToDo Not approved yet
            //$customFiles['js'][] = $customJsPath.$customJsFile;
        }

        // check if customer.css file exist and add to customFiles
        if (file_exists($customCssSearchPath.$customCssFile)) {
            $customFiles['css'][] = $customCssPath.$customCssFile;
        }

        // check if customer.css file exist in media folder and add to customFiles
        if (file_exists($customCssMediaSearchPath.$customCssFile)) {
            $customFiles['css'][] = $customCssMediaPath.$customCssFile;
        }

        return $customFiles;
    }

    /**
     * @return array
     */
    protected function getVersions(): array
    {
        $root = $this->aptoParameter->get('kernel.project_dir');
        $composerLock = json_decode(file_get_contents($root . '/composer.lock'), true);

        $versions = [
            'core' => [],
            'plugins' => []
        ];

        foreach ($composerLock['packages'] as $package) {
            $currentPackage = [
                'name' => $package['name'],
                'version' => $package['version']
            ];

            if (substr($currentPackage['name'], 0, 5) === "apto-one/") {
                $versions['core'][] = $currentPackage;
            } else if (substr($currentPackage['name'], 0, 12) === "apto-one-plugin/") {
                $versions['plugins'][] = $currentPackage;
            }
        }

        return $versions;
    }

    /**
     * @return string
     */
    protected function getPublicFolder(): string
    {
        return $this->aptoParameter->has('public_folder') ? $this->aptoParameter->get('public_folder') : '';
    }

    /**
     * @return string
     */
    protected function getMediaRelativePath(): string
    {
        return $this->aptoParameter->get('media_relative_path');
    }
}
