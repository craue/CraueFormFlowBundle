<?php

namespace Craue\FormFlowBundle\Storage;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Representation of a file handled with Gaufrette. Only supports <code>UploadedFile</code> currently.
 *
 * @author Kevin Cerro <kevincerro1997@gmail.com>
 * @copyright 2020 Kevin Cerro
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class GaufretteFile
{
    /**
     * @var string Name of the file provided by Gaufrette on upload
     */
    private $fileName;

    private $clientOriginalName;
    private $clientMimeType;

    /**
     * @param string $filename
     * @param $originalFile
     */
    public function __construct(string $filename, $originalFile)
    {
        if (!self::isSupported($originalFile)) {
            throw new InvalidTypeException($originalFile, UploadedFile::class);
        }

        //Filename of uploaded file with Gaufrette
        $this->fileName = $filename;

        //Keep client original name and mime type
        $this->clientOriginalName = $originalFile->getClientOriginalName();
        $this->clientMimeType = $originalFile->getClientMimeType();
    }

    /**
     * @param mixed $file
     * @return bool
     */
    public static function isSupported($file)
    {
        return $file instanceof UploadedFile;
    }
}
