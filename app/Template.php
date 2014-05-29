<?php
namespace app;

use app\InjectionConverter;

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
abstract class Template
{
    /**
     * @var array The collection of templates
     */
    protected $tags;

    /**
     * @var int
     */
    protected $allTagCount;

    /**
     * @var int
     */
    protected $differentTagCount;

    /**
     * @var array Supplementary information about the tags
     */
    protected $tagInfo;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array The path of the files which are converted
     */
    protected $fileNames;

    /**
     * @var string
     */
    protected $projectName;

    /**
     * @var string
     */
    protected $templateCollectorRegex;

    /**
     * @var array The content of the files
     */
    protected $fileContents;

    /**
     * @var string The name of extensions
     */
    protected $extension;

    /**
     * @var \app\InjectionConverter|null
     */
    protected $injectionConverter= null;

    /**
     * @var array:\app\Converter The collection of converters
     */
    protected $converters= array();

    /**
     * @param \app\Template $template
     */
    protected function setSourceTemplate(Template $template)
    {
        $this->setTemplateCollectorRegex($template->getTemplateCollectorRegex());
    }

    /**
     * @param string $extension
     * @param string $path
     * @param string $projectName
     */
    public function __construct($extension = "tpl", $path = null, $projectName = "")
    {
        $this->fileNames= array();
        $this->fileContents= array();
        $this->projectName= $projectName;
        $this->tags = array();
        $this->tagInfo = array();
        $this->extension= $extension;
        $this->path= $path;
        $this->setConverters();
    }

    /**
     * Add the converters here.
     * @return mixed
     */
    abstract protected function setConverters();

    /**
     * @return string
     */
    abstract protected function getTempDirectory();

    /**
     * Converts from PHP.
     * @return mixed
     */
    abstract public function convertFromPHP();

    /**
     * Saves conversion to files
     */
    final public function saveConversion()
    {
        //Convert tags
        $info= $this->getTagInfo();
        foreach ($this->getConvertedTags() as $key => $tag) {
            foreach ($info[$key]["fileNames"] as $fileName) {
                $this->fileContents[$fileName]= str_replace($key, $tag, $this->fileContents[$fileName]);
                file_put_contents(realpath($fileName), $this->fileContents[$fileName]);
            }
        }
    }

    /**
     * @param string $toExtension
     */
    final public function renameFileExtensions($toExtension)
    {
        foreach ($this->fileNames as $fileName) {
            $extension= pathinfo($fileName, PATHINFO_EXTENSION);
            rename($fileName, substr_replace($fileName, $toExtension, -strlen($extension)));
        }
    }

    protected function convertInjected($projectName)
    {
        $this->injectionConverter->setInjectionsName($projectName);
        foreach ($this->fileContents as &$templateFileContent) {
            $templateFileContent= $this->injectionConverter->convert($templateFileContent);
        }
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Initializes the template file information and collects the tags.
     */
    final public function initializeConversion()
    {
        $this->injectionConverter= new InjectionConverter();
        $this->setTemplateFileInfo();
        $this->convertInjected($this->projectName);
        $this->setTags();
    }

    /**
     * Collects template tags.
     */
    final private function setTags()
    {
        $this->allTagCount= 0;
        $this->differentTagCount= 0;
        foreach ($this->fileContents as $fileName => $content) {
            $matches = array();
            if ($content != null && empty($content) === false) {
                $content = preg_replace("/\n\r/", "\n", $content);
                preg_match_all($this->getTemplateCollectorRegex(), $content, $matches);
                if ($matches != null && empty($matches[0]) === false) {
                    foreach ($matches[0] as $m) {
                        if (isset($this->tags[$m]) == false) {
                            $this->tags[$m]= $m;
                            $this->tagInfo[$m]= array("fileNames" => array(), "count" => 0);
                            $this->differentTagCount++;
                        }
                        $this->tagInfo[$m]["fileNames"][]= $fileName;
                        $this->tagInfo[$m]["count"]++;
                        $this->allTagCount++;
                    }
                }
            }
        }
    }

    /**
     * Az eddig már módosított tageket adja vissza.
     * @return array
     */
    final protected function getConvertedTags()
    {
        $result = array();

        foreach ($this->tags as $k => $v) {
            if ($k != $v) {
                $result[$k] = $v;
            }
        }

        return $result;
    }

    /**
     * Az eddig még nem módosított template-eket adja vissza.
     * @return array
     */
    final protected function getRemainingTags()
    {
        $result = array();

        foreach ($this->tags as $k => $v) {
            if ($k == $v) {
                $result[$k] = $v;
            }
        }

        return $result;
    }

    final protected function setTemplateFileInfo()
    {
        // Collect file names
        $filesObject = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->path),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        // Filter files by their extension and store their name and content
        foreach ($filesObject as $name => $object) {
            if (pathinfo($name, PATHINFO_EXTENSION) == $this->getExtension()) {
                $this->fileNames[] = $name;
                $this->fileContents[$name]= file_get_contents($name);
            }
        }
    }

    /**
     * Template-ek txt fájlba írása.
     * @param string $toFile
     * @param array $templates
     */
    final protected function writeTagsToFile($toFile, array $templates)
    {
        $content = "";
        foreach ($templates as $k => $v) {
            $content .= "$k\n\n$v\n---------------------------------------------------------------------------------\n";
        }
        if (is_dir($this->getTempDirectory()) != true) {
            mkdir($this->getTempDirectory());
        }
        file_put_contents(realpath($this->getTempDirectory()) . "/$toFile.txt", $content);
    }

    /**
     * @param string $input
     * @return string
     */
    final protected function convertTag($input)
    {
        $output= $input;

        foreach ($this->converters as $converter) {
            if ($converter instanceof Converter) {
                $output = $converter->convert($output);

                // If conversion is successful then finishing
                if ($output != $input) {
                    break;
                }
            }
        }

        return $output;
    }

    /**
     * @param \app\Converter $converter
     */
    final protected function addConverter(Converter $converter)
    {
        $this->converters[]= $converter;
    }

    /**
     * Prints the information about the conversion.
     */
    final public function printConversionInfo()
    {
        $differentSum= 0;

        echo $this->injectionConverter->getName() . "<br/>";
        $this->injectionConverter->printConversionInfo();
        echo "----------------------------------------------<br/>";

        foreach ($this->converters as $converter) {
            if ($converter instanceof Converter) {
                $differentSum+= $converter->getConversionInfoDifferentSum();

                echo $converter->getName() . "<br/>";
                $converter->printConversionInfo();
                echo "----------------------------------------------<br/>";
            }
        }

        $allSum= 0;

        if (empty($this->tagInfo) == false) {
            foreach ($this->tagInfo as $key => $info) {
                if (isset($this->tags[$key]) && $key != $this->tags[$key]) {
                    $allSum += $info["count"];
                }
            }
        }

        $rateDifferent= $this->differentTagCount != 0? round($differentSum / $this->differentTagCount, 5)* 100 : 0;
        $rateAll= $allSum != 0? round($allSum / $this->allTagCount, 5) * 100 : 0;

        echo "----------------------------------------------<br/>";
        echo "INJECTIONS CONVERTED:<br/>";
        echo "Different: " . $this->injectionConverter->getConversionInfoDifferentSum() . "<br/>";
        echo "All: " . $this->injectionConverter->getAllConversionSum() . "<br/>";

        echo "----------------------------------------------<br/>";
        echo "TAGS CONVERTED:<br/>";
        echo "<table>";
        echo "<tr><th></th><th>Converted</th><th>From</th><th>Rate</th></tr>";
        echo "<tr><td>Different:</td><td>$differentSum</td><td>$this->differentTagCount</td><td>".
            $rateDifferent . " %</td></tr>";
        echo "<tr><td>All:</td><td>$allSum</td><td>$this->allTagCount</td><td>" .
            $rateAll . " %</td></tr>";
    }

    /**
     * @return InjectionConverter|null
     */
    public function getInjectionConverter()
    {
        return $this->injectionConverter;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return array
     */
    public function getFileContents()
    {
        return $this->fileContents;
    }

    /**
     * @return array
     */
    public function getFileNames()
    {
        return $this->fileNames;
    }

    /**
     * @param array $tagInfo
     */
    final public function setTagInfo($tagInfo)
    {
        $this->tagInfo = $tagInfo;
    }

    /**
     * @return array
     */
    final public function getTagInfo()
    {
        return $this->tagInfo;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * @return string
     */
    public function getTemplateCollectorRegex()
    {
        return $this->templateCollectorRegex;
    }

    /**
     * @param string $templateCollectorRegex
     */
    public function setTemplateCollectorRegex($templateCollectorRegex)
    {
        $this->templateCollectorRegex = $templateCollectorRegex;
    }

    /**
     * @return mixed
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * @param mixed $projectName
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;
    }

    /**
     * @return int
     */
    public function getAllTagCount()
    {
        return $this->allTagCount;
    }

    /**
     * @return int
     */
    public function getDifferentTagCount()
    {
        return $this->differentTagCount;
    }
}
