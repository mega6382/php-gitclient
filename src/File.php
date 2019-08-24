<?php

namespace GitClient;

/**
 * The class for containing the file data
 * Class File
 * @package GitClient
 */
class File
{
    public $name;
    public $file;
    public $fullPath;
    public $relativePath;
    public $content;

    public function __construct($name, $file, $fullPath, $relativePath, $content)
    {
        $this->name = $name;
        $this->file = $file;
        $this->fullPath = $fullPath;
        $this->relativePath = $relativePath;
        $this->content = $content;
    }
}
