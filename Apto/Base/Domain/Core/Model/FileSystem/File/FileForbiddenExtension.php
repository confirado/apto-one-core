<?php

namespace Apto\Base\Domain\Core\Model\FileSystem\File;

class FileForbiddenExtension extends \Exception
{

    /**
     * FileForbiddenExtension constructor.
     * @param string $file
     */
    public function __construct($file)
    {
        parent::__construct('The file "' . $file . '" has a forbidden extension.');
    }

}