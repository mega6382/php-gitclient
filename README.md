# PHP Git Client

A simple git client for getting latest files from a given branch (uses git cli client at the moment, might change this later)

## Installation

1. Clone the project.
1. Includes the files into your code, or if you are using then include the composer's autoload file.

Example:
```php
<?php

use GitClient\Configurations;
use GitClient\GitClient;

include "vendor/autoload.php";

$config = new Configurations();
$config->setRepository("https://github.com/mega6382/HabWpLogin");
$config->setLogFile("README.md");
$client = new GitClient($config);

if($client->connect()) {
    // All repo files
    var_dump(iterator_to_array($client->getNextFile(), true));
    // Log files
    var_dump(iterator_to_array($client->getLogFile(), true));
}
$client->disconnect();

```

## Running the tests

Run `composer install` to install the dependencies. Then run `vendor/bin/phpunit` to run the tests.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

