<?php
namespace app\twig;

use app\InjectionConverter;
use app\php\PHPTemplate;
use app\Template;
use app\twig\converters\php\EchoConverter;
use app\twig\converters\php\ForeachConverter;
use app\twig\converters\php\IfConverter;
use app\twig\converters\php\PHPConverter;
use app\twig\converters\php\StaticConverter;

/**
 * Az osztály rövid leírása
 *
 * Az osztály hosszú leírása, példakód
 * akár több sorban is
 *
 * @package
 * @author kocsismate
 * @since 2014.05.12. 10:14
 */
final class TwigTemplate extends Template
{
    /**
     * @see \app\Template::setConverters()
     */
    protected function setConverters()
    {
        $this->addConverter(new StaticConverter());
        $this->addConverter(new EchoConverter());
        $this->addConverter(new IfConverter());
        $this->addConverter(new ForeachConverter());
    }

    /**
     * @see \app\Template::getTempDirectory()
     */
    protected function getTempDirectory()
    {
        return __DIR__ . "/../../temp";
    }

    /**
     * @see \app\Template::convertFromPHP()
     */
    public function convertFromPHP()
    {
        $this->setSourceTemplate(new PHPTemplate());
        $this->initialize();

        // Convert tags and write files
        $this->convertInjected($this->projectName);
        $this->convertPHPTags();
        $this->writeTagsToFile($this->projectName . "-converted", $this->getConvertedTags());
        $this->writeTagsToFile($this->projectName . "-remaining", $this->getRemainingTags());
    }

    protected function convertPHPTags()
    {
        foreach ($this->tags as &$tag) {
            $u= $tag;

            // Automatic conversion
            $u = $this->convertTag($u);

            // Fallback if there was no conversion and the input has multiple lines
            if ($u == $tag && substr_count($tag, "\n") >= 1) {
                $success= true;
                $lines= explode("\n", $tag);
                $count= count($lines);
                for ($i= 0; $i < $count && $success === true; $i++) {
                    // Converting lines into PHP templates
                    if ($i != 0) {
                        $lines[$i]= "<?php " . $lines[$i];
                    }
                    if ($i != $count - 1) {
                        $lines[$i].= " ?>";
                    }
                    $line= $lines[$i];

                    // Converting templates into the desired format
                    $line= $this->convertTag($line);

                    // If the line is converted to the source format then saving the "working copy", otherwise failing
                    if ($line != $lines[$i]) {
                        $lines[$i]= $line;
                    } else {
                        $success= false;
                    }
                }

                // If all the lines were converted then converting the whole template
                if ($success === true) {
                    $u= implode("\n", $lines);
                }
            }

            $tag= $u;
        }
    }
}
