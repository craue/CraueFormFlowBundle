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

    private $gaufretteStorage;

    /**
     * @var string Name of the file provided by Gaufrette on upload
     */
    private $fileName;

    private $clientOriginalName;
    private $clientMimeType;

    /**
     * @param GaufretteStorage $gaufretteStorage
     * @param string $filesystem
     * @param mixed $file An object meant to be serialized.
     */
    public function __construct(GaufretteStorage $gaufretteStorage, string $filesystem, $file)
    {
        if (!self::isSupported($file)) {
            throw new InvalidTypeException($file, UploadedFile::class);
        }

        //Upload file with Gaufrette
        $this->gaufretteStorage = $gaufretteStorage;
        $this->fileName = $this->gaufretteStorage->doUpload($filesystem, $file);

        //Keep client original name and mime type
        $this->clientOriginalName = $file->getClientOriginalName();
        $this->clientMimeType = $file->getClientMimeType();
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
