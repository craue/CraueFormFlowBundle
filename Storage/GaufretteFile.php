<?php

namespace Craue\FormFlowBundle\Storage;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Craue\FormFlowBundle\Util\TempFileUtil;
use Gaufrette\File;
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
     * @param File $file
     * @return mixed The file retrieved from Gaufrette converted to UploadedFile
     */
    public function getAsUploadedFile(File $file) {
        $tempDir = sys_get_temp_dir();

        // create a temporary file with its original content
        $tempFile = tempnam($tempDir, 'craue_form_flow_serialized_file');
        file_put_contents($tempFile, $file->getContent());

        TempFileUtil::addTempFile($tempFile);

        // avoid a deprecation notice regarding "passing a size as 4th argument to the constructor"
        // TODO remove as soon as Symfony >= 4.1 is required
        if (property_exists(UploadedFile::class, 'size')) {
            return new UploadedFile($tempFile, $this->clientOriginalName, $this->clientMimeType, null, null, true);
        }

        return new UploadedFile($tempFile, $this->clientOriginalName, $this->clientMimeType, null, true);
    }

    public function getFileName() {
        return $this->fileName;
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
