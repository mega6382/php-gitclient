<?php
declare(strict_types=1);

namespace GitClient;

class Configurations
{
    /**
     * Repository URL
     * @var string
     */
    private $repository;

    /**
     * Optional, name for the log file
     * @var string
     */
    private $log_file;

    /**
     * N/a
     * @var string
     */
    private $module;

    /**
     * path to the git client installed on your system
     * @var string
     */
    private $client = 'git';

    /**
     * Branch to get the files from
     * @var string
     */
    private $branch = 'master';

    /**
     * path to the git client installed on your system
     * @param string $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * path to the git client installed on your system
     * @return string
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Optional, name for the log file
     * @param string $log_file
     */
    public function setLogFile($log_file)
    {
        $this->log_file = $log_file;
    }

    /**
     * Optional, name for the log file
     * @return string
     */
    public function getLogFile()
    {
        return $this->log_file;
    }

    /**
     * Repository URL
     * @param $repository
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }

    /**
     * Repository URL
     * @return string
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Branch to get the files from
     * @param string $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * Branch to get the files from
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * N/a
     * @param string $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * N/a
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }
}