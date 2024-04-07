<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Controller;

use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandBus;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\Exception\ClassNotFoundException;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\Exception\CommandNotFoundException;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\Exception\CommandNotSupported;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\MessageBusManager;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\DataType;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataTypeRegistry;
use ReflectionException;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\Import;

use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataTypeNotRegisteredException;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class ImportExportController extends AbstractController
{
    /**
     * @var MediaFileSystemConnector
     */
    protected $connector;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var DataTypeRegistry
     */
    protected $dataTypeRegistry;

    /**
     * @var CommandBus
     */
    protected $commandBus;

    /**
     * @var MessageBusManager
     */
    protected $messageBusManager;

    public function __construct(MediaFileSystemConnector $connector, RouterInterface $router, DataTypeRegistry $dataTypeRegistry, CommandBus $commandBus, MessageBusManager $messageBusManager)
    {
        $this->connector = $connector;
        $this->router = $router;
        $this->dataTypeRegistry = $dataTypeRegistry;
        $this->commandBus = $commandBus;
        $this->messageBusManager = $messageBusManager;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/import-export", methods={"GET, POST"}, priority="10")
     */
    public function indexAction(Request $request): Response
    {
        $importFiles = $this->connector->getDirectoryContent(new Directory('/import'));
        $filesList = '<h1>Dateien zum Import:</h1><ul>';

        foreach ($importFiles as $file) {
            if ($file['isDir'] || 'csv' !== $file['extension']) {
                continue;
            }

            $link = $this->router->generate('apto_plugins_importexport_infrastructure_importexportbackend_importexport_import', [
                'fileName' => urlencode($file['path'])
            ]);

            $filesList .= '<li><a href="' . $link . '" target="_blank">' . $file['name'] . '</a></li>';
        }

        $filesList .= '</ul>';
        return new Response($filesList);
    }

    /**
     * @Route("/import-export/documentation")
     * @param Request $request
     * @return Response
     */
    public function documentationAction(Request $request): Response
    {
        $dataTypes = $this->dataTypeRegistry->getDataTypes();

        $dataTypesHTML = '<h2>Dokumentation Datentypen</h2>';
        $dataTypesHTML.= '<table>';
        $dataTypesHTML.= '<tr><th style="text-align: left; border-bottom: 1px solid #000;">Spalte "data-type"</th><th style="text-align: left; border-bottom: 1px solid #000;">Spalten Pflicht</th><th style="text-align: left; border-bottom: 1px solid #000;">Spalten Optional</th></tr>';

        /** @var DataType $dataType */
        foreach ($dataTypes as $dataType) {
            $dataTypesHTML.= '<tr>';
            $dataTypesHTML.= '<td style="vertical-align: top; border-bottom: 1px solid #000;"><strong>' . $dataType->getDataType() . '</strong></td>';
            $dataTypesHTML.= '<td style="vertical-align: top; border-bottom: 1px solid #000;">' . implode('<br />', $dataType->getRequiredFields()) . '</td>';
            $dataTypesHTML.= '<td style="vertical-align: top; border-bottom: 1px solid #000;">' . implode('<br />', $dataType->getOptionalFields()) . '</td>';
            $dataTypesHTML.= '</tr>';
        }

        $dataTypesHTML.= '</table';

        return new Response($dataTypesHTML);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ClassNotFoundException
     * @throws CommandNotFoundException
     * @throws CommandNotSupported
     * @throws DataTypeNotRegisteredException
     * @throws ReflectionException
     * @throws \Throwable
     */
    public function importAction(Request $request): Response
    {

        $host = $request->getHost();
        $locale = 'de_DE';
        $fileName = urldecode($request->get('fileName'));
        $errors = $this->importFile($host, $locale, $this->connector->getAbsolutePath($fileName));

        if (count($errors) > 0) {
            print_r($errors);
        }

        return new Response();
    }

    /**
     * @Route("/import-export/export", methods={"GET, POST"}, priority="10")
     * @param Request $request
     * @return Response
     */
    public function exportAction(Request $request): Response
    {
        return new Response();
    }

    /**
     * @Route("/import-export/import-upload", methods={"POST"}, priority="10")
     * @param Request $request
     * @return Response
     * @throws ClassNotFoundException
     * @throws CommandNotFoundException
     * @throws CommandNotSupported
     * @throws DataTypeNotRegisteredException
     * @throws ReflectionException
     * @throws \Throwable
     */
    public function importUploadAction(Request $request): Response
    {
        $errors = [];
        $files = $this->getFiles($request);
        $arguments = $request->get('arguments', []);

        if (!array_key_exists('host', $arguments)) {
            throw new \InvalidArgumentException('Argument host is missing.');
        }

        if (!array_key_exists('locale', $arguments)) {
            throw new \InvalidArgumentException('Argument locale is missing.');
        }

        foreach ($files as $file => $fileName) {
            $errors[] = [
                'file' => $fileName,
                'errors' => $this->importFile($arguments['host'], $arguments['locale'], $file)
            ];
        }

        return new Response(json_encode($errors));
    }

    /**
     * @param string $host
     * @param string $locale
     * @param string $filePath
     * @return array
     * @throws ClassNotFoundException
     * @throws CommandNotFoundException
     * @throws CommandNotSupported
     * @throws DataTypeNotRegisteredException
     * @throws ReflectionException
     * @throws \Throwable
     */
    private function importFile(string $host, string $locale, string $filePath): array
    {
        $file = File::createFromPath($filePath);
        $import = new Import($this->dataTypeRegistry);

        $import->importCsvFile($file);
        $errors = [];

        if ($import->hasErrors()) {
            $errors = $this->processErrors($import);
        } else {
            $preProcessErrors = $this->preProcessValidLines($host, $locale, $import);
            $processErrors = $this->processValidLines($host, $locale, $import);
            $errors = array_merge($errors, $preProcessErrors, $processErrors);
        }

        return $errors;
    }

    /**
     * @param string $host
     * @param string $locale
     * @param Import $import
     * @return array
     * @throws ReflectionException
     * @throws ClassNotFoundException
     * @throws CommandNotFoundException
     * @throws CommandNotSupported
     * @throws \Throwable
     */
    private function preProcessValidLines(string $host, string $locale, Import $import): array
    {
        $errors = [];

        /** @var DataType $dataType */
        foreach ($this->dataTypeRegistry->getDataTypes() as $dataType) {
            $dataTypeName = $dataType->getDataType();

            if (null === $dataType->getPreImportCommand() || !$import->hasDataType($dataTypeName)) {
                continue;
            }

            $command = $this->messageBusManager->getCommand(
                $dataType->getPreImportCommand(),
                [
                    $locale,
                    $host,
                    $import->getValidLinesByDataType($dataTypeName)
                ]
            );

            try {
                $this->commandBus->handle($command);
            } catch (\Exception $exception) {
                $errors[] = $exception->getMessage();
            }
        }

        return $errors;
    }

    /**
     * @param string $host
     * @param string $locale
     * @param Import $import
     * @return array
     * @throws ClassNotFoundException
     * @throws CommandNotFoundException
     * @throws CommandNotSupported
     * @throws DataTypeNotRegisteredException
     * @throws ReflectionException
     * @throws \Throwable
     */
    private function processValidLines(string $host, string $locale, Import $import): array
    {
        $errors = [];

        foreach ($import->getValidLines() as $validLine) {
            $dataType = $this->dataTypeRegistry->getDataType($validLine['data-type']);
            $command = $this->messageBusManager->getCommand($dataType->getImportCommand(), [$locale, $host, $validLine]);

            try {
                $this->commandBus->handle($command);
            } catch (\Exception $exception) {
                $errors[] = $exception->getMessage();
            }
        }

        return $errors;
    }

    /**
     * @param Import $import
     * @return array
     */
    private function processErrors(Import $import): array
    {
        return $import->getErrors();
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getFiles(Request $request): array
    {
        $files = [];

        /** @var UploadedFile $file */
        foreach ($request->files as $file) {
            if (is_array($file)) {
                /** @var UploadedFile $subFile */
                foreach ($file as $subFile) {
                    $files[$subFile->getPathname()] = $subFile->getClientOriginalName();
                }
            } else {
                $files[$file->getPathname()] = $file->getClientOriginalName();
            }
        }

        return $files;
    }
}