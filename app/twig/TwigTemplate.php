<?php
namespace app\twig;

use app\InjectionConverter;
use app\php\PHPTemplate;
use app\Template;
use app\twig\converters\php\EchoConverter;
use app\twig\converters\php\ForeachConverter;
use app\twig\converters\php\IfConverter;
use app\twig\converters\php\PHPConverter;
use app\twig\converters\php\AssignmentConverter;
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
     * @var boolean
     */
    protected $isConvertStaticMethods= false;

    /**
     * @see \app\Template::setConverters()
     */
    protected function setConverters()
    {
        $this->clearConverters();
        if ($this->isConvertStaticMethods() === true) {
            $this->addConverter(new StaticConverter());
        }
        $this->addConverter(new AssignmentConverter());
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
        $this->setConverters();
        $this->setSourceTemplate(new PHPTemplate());
        $this->initializeConversion();

        // Convert tags and write files
        $this->convertPHPTags();
        $this->writeTagsToFile($this->projectName . "-successful", $this->getConvertedTags());
        $this->writeTagsToFile($this->projectName . "-unsuccessful", $this->getRemainingTags(), false);
    }

    protected function convertPHPTags()
    {
        $partialConversions= array();

        foreach ($this->tags as &$tag) {
            $u= $tag;

            // Automatic conversion
            $u = $this->convertTag($u);

            // Fallback if there was no conversion and the input has multiple lines
            if ($u == $tag && substr_count($tag, "\n") >= 1) {
                $isOneConverted= false;
                $isAllConverted= true;
                $lines= explode("\n", $tag);
                $count= count($lines);

                for ($i= 0; $i < $count; $i++) {
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
                        $isOneConverted= true;
                    } else {
                        $isAllConverted= false;
                    }
                }

                // If all the lines were converted then converting the whole template
                if ($isAllConverted === true) {
                    $u= implode("\n", $lines);
                }
                // If the lines are only partially converted then saving them in the $partialConversions array
                if (
                    $this->isIsPartialConversionEnabled() &&
                    $this->$isAllConverted === false &&
                    $isOneConverted === true
                ) {
                    $partialConversions[$tag]= implode("\n", $lines);
                }
            }

            $this->writeTagsToFile($this->projectName . "-partial", $partialConversions);

            $tag= $u;
        }
    }

    /**
     * @return boolean
     */
    public function isConvertStaticMethods()
    {
        return $this->isConvertStaticMethods;
    }

    /**
     * @param boolean $isConvertStaticMethods
     */
    public function setIsConvertStaticMethods($isConvertStaticMethods)
    {
        $this->isConvertStaticMethods = $isConvertStaticMethods;
    }
}
