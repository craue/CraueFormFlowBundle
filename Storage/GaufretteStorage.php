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
        $randomName = $this->generateRandomName($file->getExtension());

        if ($filesystem->getAdapter() instanceof MetadataSupporter) {
            $filesystem->getAdapter()->setMetadata($randomName, ['contentType' => $file->getMimeType()]);
        }

        $filesystem->write($randomName, file_get_contents($file->getPathname()), true);
        return $randomName;
    }

    public function doRemove(string $filesystem, string $name)
    {
        $filesystem = $this->getFilesystem($filesystem);

        try {
            return $filesystem->delete($name);
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
     * @param string $extension
     * @return string|string[]
     */
    private function generateRandomName(string $extension)
    {
        $name = str_replace('.', '', \uniqid('', true));
        return sprintf('%s.%s', $name, $extension);
    }
}