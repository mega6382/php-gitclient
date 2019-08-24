<?php

use GitClient\Configurations;
use GitClient\GitClient;

include "vendor/autoload.php";

$config = new Configurations();
$config->setRepository("https://github.com/mega6382/base64");
$client = new GitClient($config);

var_dump($client->connect());
var_dump(iterator_to_array($client->getNextFile(), true));
var_dump($client->disconnect());