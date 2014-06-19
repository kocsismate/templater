#templater

I developed this library for a project where I had to replace the legacy PHP templates to Twig templates. The job was
enormous: we had more than 16,000 PHP tags (sometimes with dozens of lines) in 400+ files.
 
So thanks to Templater I managed to successfully convert (almost barely) with automatic techniques, the 90% of the
different tags, and the 81% of all the tags (the majority of the difference comes from the fact that block endings, like
"}" cannot be converted without extra work).
 
So only ~2200 tags remained for manual conversion, but it came evident soon, that it's still too slow for me. Then I
introduced "partial conversion", which means that a log file stores those tags which couldn't be fully converted,
but some parts of them. It's very safe and useful for long, multiline tags: your templates are not threatened with
partially working tags but you can copy the most parts of them to your template file.

##Suggested working method:

Copy your original templates from your project's template directory to a safe location (e.g. name the folder as
"my-converted-templates"). Then make another backup of it (e.g.: name it as "my-original-templates"). Now you can use
Templater to modify your files in the "my-converted-templates" directory. Finally start "merging" your new templates
with the original ones in your project. Remember to check for partial conversions! If you have to re-convert the tags,
you can replace "my-converted-templates" with "my-original-templates" without affecting your manually converted tags.

##Usage:

```php
<?php
require "vendor/autoload.php";

use app\twig\TwigTemplate;

$converter= new TwigTemplate();

$converter->convertFromPHP("extension_of_templates", "/path/of/templates", "name_of_the_project");
$converter->setIsConvertStaticMethods(false);
$converter->printConversionInfo();
$converter->saveConversion();
$converter->renameFileExtensions("twig");
```

In the temp directory, you will get two files:
- {name_of_the_project}-unsuccessful: These tags are not yet converted
- {name_of_the_project}-partial: These tags could have only been partially converted
- {name_of_the_project}-successful: These tags were converted fully and successfully

##Tag injection:

You can inject your own searched and replacement tags to Templater. You have to add a {name_of_the_project}-injection.php file to the "temp" directory which returns an array. An example is:

```php
return array(
    '/<\?php echo \$header; \?>/' => '{% include "header.twig" %}',
);
```

It will convert those tags which echo a variable named "header" into an included Twig template. Important to know,
that Templater starts with the conversion of possible injected tags then continues with the automatic replacements.

##Static method conversion:

You can also set static method conversion with TwigTemplate::setIsConvertStaticMethods(). Currently, Templater can only
convert PHP static method invocations with the following signature:
```php
<?php echo ClassName::methodName(); ?>
```
into Twig tags with the following signature:
```
{{ staticCall("ClassName", "methodName") }}
```
You have to ensure that a "staticCall" function is supported by your Twig environment (e.g.: by extending it).
