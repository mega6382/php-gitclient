<?php

namespace GitClient;

/**
 * The file manager to handle the process of reading the repo files
 * Class FilesManager
 * @package GitClient
 */
class FilesManager
{
    /**
     * Location of the cloned repo
     * @var string
     */
    private $location;
    /**
     * Absolute Location of the cloned repo
     * @var bool|string
     */
    private $absoluteLocation;

    /**
     * The directories that need to be ignored
     * @var array
     */
    private $blockedDirectories = [
        ".git"
    ];

    /**
     * Set the location to the cloned repo
     * @param string $location
     */
    public function setLocation(string $location)
    {
        $this->location = $location;
        $this->absoluteLocation = realpath($location);
    }

    /**
     * Prepare file data for a single file
     * @param \DirectoryIterator $fileInfo
     * @return File
     */
    private function prepareFileInfo(\DirectoryIterator $fileInfo)
    {
        $fullPath = realpath($fileInfo->getPathname());
        $relativePath = $fileInfo->getPathname();
        if (substr($fullPath, 0, strlen($this->absoluteLocation)) == $this->absoluteLocation) {
            $relativePath = substr($fullPath, strlen($this->absoluteLocation));
        }

        return new File(
            $fileInfo->getFilename(),
            $fileInfo->getPathname(),
            realpath($fileInfo->getPathname()),
            $relativePath,
            file_get_contents($fileInfo->getPathname())
        );

    }

    /**
     * Get files and their contents from the repo
     * @param string $dir
     * @param null|string $fileName Use this to only get the files that match the name
     * @return \Generator
     */
    private function getFilesAndContents(string $dir, $fileName = null)
    {

        foreach (new \DirectoryIterator($dir) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            } else if ($fileInfo->isDir() && !in_array($fileInfo->getFilename(), $this->blockedDirectories)) {
                yield from $this->getFilesAndContents($fileInfo->getPathname());
            } else if ($fileInfo->isFile()) {
                if ($fileName && $fileInfo->getFilename() !== $fileName) {
                    continue;
                }
                yield $this->prepareFileInfo($fileInfo);
            }

        }
    }

    /**
     * File Manager, that reads the files inside the cloned repo's checkedout branch
     * @param string|null $logFile
     * @return \Generator
     * @throws ConfigurationException
     */
    private function filesManager(string $logFile = null)
    {

        if (!$this->location) {
            throw new ConfigurationException("location not set");
        }
        yield from $this->getFilesAndContents($this->location, $logFile);
    }

    /**
     * Get the data for files in the repo
     * @return \Generator
     * @throws ConfigurationException
     */
    public function getRepoFiles()
    {
        yield from $this->filesManager();
    }

    /**
     * Get the data for the log files in the repo
     * @param string $logFile
     * @return \Generator
     * @throws ConfigurationException
     */
    public function getLogFiles(string $logFile)
    {
        yield from $this->filesManager($logFile);
    }

}