#templater

Currently, the library supports conversion of PHP templates into Twig Templates.

##Usage:

```php
<?php
require "vendor/autoload.php";

use app\twig\TwigTemplate;

$converter= new TwigTemplate();

$converter->convertFromPHP("extension_of_templates", "/path/of/templates", "name_of_the_project");
$converter->printConversionInfo();
$converter->saveConversion();
$converter->renameFileExtensions("twig");
```

In the temp directory, you will get two files:
- {name_of_the_project}-converted: These tags were converted successfully
- {name_of_the_project}-remaining: These tags are not yet converted

##Tag injection:

You can inject your own searched and replacement tags to Templater. You have to add a "name_of_the_project".php file to the "temp" directory which returns an array. An example is:

```php
return array(
    '/<\?php echo \$header; \?>/' => '{% include "header.twig" %}',
);
```

It will convert those tags which echo a header variable to an included Twig template. Important to know, that Templater
starts with the conversion of possible injected tags then continues with the automatic replacements.

##Static method conversion:

Currently, Templater can only convert PHP static method calls with the following signature 
```php
<?php echo ClassName::methodName(); ?>
```
to the Twig tags with the following signature:
```
{{ staticCall("ClassName", "methodName") }}
```
You have to ensure that a "staticCall" function is supported by your Twig environment (e.g.: by extending it).
