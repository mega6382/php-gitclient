<?php
declare(strict_types=1);

namespace GitClient;

/**
 * The main class for this "library"
 * Class GitClient
 * @package GitClient
 */
class GitClient
{
    /**
     * @var Configurations
     */
    private $config;
    /**
     * Exact location of the cloned repo
     * @var string
     */
    private $tmpLocation;
    /**
     * The base directory where the cloned git repos will be stored temporarily
     * Change this according to your need
     * @var string
     */
    private $tmpDirectory = "/tmp";

    /**
     * Contains the status of whether we are connected to the repo or not
     * @var bool
     */
    private $connected;

    /**
     * The file manager to handle the process of reading the repo files
     * @var FilesManager
     */
    private $filesManager;

    public function __construct(Configurations $config)
    {
        $this->configure($config);
        $this->filesManager = new FilesManager();
    }

    /**
     * Set configuration, and check if value for repository is set or not
     * @param Configurations $config
     * @throws ConfigurationException
     */
    private function configure(Configurations $config)
    {
        if (empty($config->getRepository()) || strlen($config->getRepository()) < 1) {
            throw new ConfigurationException("Repository is required");
        }
        $this->config = $config;
    }

    /**
     * Used to execute shell commands
     * @param string $action
     * @return array
     */
    private function execCommandForClient($action)
    {
        $command = "{$this->config->getClient()} {$action} 2>&1";
        exec($command, $output, $exitCode);

        return ['output' => $output, 'exitCode' => $exitCode];
    }

    /**
     * Generate the location for where the repo will be stored, temporarily
     */
    private function generateTmpLocation()
    {
        $CloneDirectoryName = preg_replace("/[^0-9a-zA-Z]/m", "", $this->config->getRepository()) . time();
        $this->tmpLocation = $this->tmpDirectory . '/' . $CloneDirectoryName;
    }

    /**
     * Clone the Repo in the generated tmp location
     * @return array
     */
    private function cloneRepo()
    {
        $action = "clone {$this->config->getRepository()} {$this->tmpLocation}";
        return $this->execCommandForClient($action);
    }

    /**
     * Establish a connection with the remote repo and download its contents
     * @return bool
     * @throws ConfigurationException
     */
    public function connect()
    {
        $this->generateTmpLocation();
        $clone = $this->cloneRepo();
        if ($clone['exitCode'] !== 0) {
            throw new ConfigurationException("Unable to connect to remote repo, or invalid repo provided");
        }
        $this->filesManager->setLocation($this->tmpLocation);
        $this->connected = true;
        return true;
    }

    /**
     * Delete the contents of the cloned repo
     * @param string $dirname
     * @return bool
     */
    private function deleteClonedRepoContent($dirname)
    {
        $dir_handle = null;
        if (is_dir($dirname)) {
            $dir_handle = opendir($dirname);
        }
        if (!$dir_handle) {
            return false;
        }
        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname . "/" . $file)) {
                    chmod($dirname . "/" . $file, 0777);
                    unlink($dirname . "/" . $file);
                } else {
                    $this->deleteClonedRepoContent($dirname . '/' . $file);
                }
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

    /**
     * Disconnect form the repo by deleting the clone
     * @return bool
     * @throws ConfigurationException
     */
    public function disconnect()
    {
        if (!$this->connected) {
            throw new ConfigurationException("Not connected to any repo");
        }

        if (!$this->tmpLocation) {
            throw new ConfigurationException("No cloned repo found");
        }

        if (!$this->deleteClonedRepoContent($this->tmpLocation)) {
            throw new ConfigurationException("Unable to delete the cloned repo");
        }
        $this->connected = false;
        return true;
    }

    /**
     * Checkout the branch provided in the config
     * @return bool
     * @throws ConfigurationException
     */
    public function checkout()
    {
        $clone = $this->execCommandForClient("checkout {$this->config->getBranch()}");
        if ($clone['exitCode'] !== 0) {
            throw new ConfigurationException("Unable to checkout the branch {$this->config->getBranch()}.");
        }
        return true;
    }

    /**
     * Get files from the repo
     * @return \Generator
     * @throws ConfigurationException
     */
    public function getNextFile()
    {
        yield from $this->filesManager->getRepoFiles();
    }

    /**
     * Get the data for the log files
     * @return \Generator
     * @throws ConfigurationException
     */
    public function getLogFile()
    {
        if (!$this->config->getLogFile()) {
            throw new ConfigurationException("LogFile not set");
        }
        yield from $this->filesManager->getLogFiles($this->config->getLogFile());
    }

    /**
     * Check if the provided repo is valid or not
     * @return bool
     */
    public function validate()
    {
        $action = "ls-remote {$this->config->getRepository()}";
        $data = $this->execCommandForClient($action);
        return $data['exitCode'] === 0;
    }
}
