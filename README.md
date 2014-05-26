#templater

Currently, the library supports converting from PHP templates to Twig Templates.

Usage:

```php
<?php
require "vendor/autoload.php";

use app\twig\TwigTemplate;

$converter= new TwigTemplate();

$converter->convertFromPHP("extension_of_templates", "/path/of/templates", "name_of_the_project");
$converter->printConversionInfo();
$converter->saveConversion();
```

In the temp directory, you will get two files:
- {name_of_the_project}-converted: These tags were converted successfully
- {name_of_the_project}-remaining: These tags are not yet converted
