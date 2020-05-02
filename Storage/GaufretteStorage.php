<?php


namespace Craue\FormFlowBundle\Storage;


use Gaufrette\Adapter\MetadataSupporter;
use Gaufrette\Exception\FileNotFound;
use Gaufrette\FilesystemInterface;
use Gaufrette\FilesystemMapInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class GaufretteStorage
{
    private $filesystemMap;

    /**
     * Constructs a new instance of GaufretteStorage.
     *
     * @param FilesystemMapInterface $filesystemMap
     */
    public function __construct(FilesystemMapInterface $filesystemMap)
    {
        $this->filesystemMap = $filesystemMap;
    }

    public function doUpload(string $filesystem, UploadedFile $file)
    {
        $filesystem = $this->getFilesystem($filesystem);
        $randomName = $this->generateRandomName();

        if ($filesystem->getAdapter() instanceof MetadataSupporter) {
            $filesystem->getAdapter()->setMetadata($randomName, ['contentType' => $file->getMimeType()]);
        }

        $filesystem->write($randomName, file_get_contents($file->getPathname()), true);
        return $randomName;
    }

    public function doDownload(string $filesystem, GaufretteFile $gaufretteFile)
    {
        $filesystem = $this->getFilesystem($filesystem);
        return $filesystem->get($gaufretteFile->getFileName());
    }

    public function doRemove(string $filesystem, string $fileName)
    {
        $filesystem = $this->getFilesystem($filesystem);

        try {
            return $filesystem->delete($fileName);
        } catch (FileNotFound $e) {
            return false;
        }
    }


    /**
     * Get filesystem adapter from the property mapping.
     * @param string $filesystem
     * @return FilesystemInterface
     */
    private function getFilesystem(string $filesystem): FilesystemInterface
    {
        return $this->filesystemMap->get($filesystem);
    }

    /**
     * Generates random name
     * @todo May we can improve this by setting the extension on the random name
     * @return string|string[]
     */
    private function generateRandomName()
    {
        return str_replace('.', '', \uniqid('', true));
    }
}