<?php
namespace app;

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
     * @var array Supplementary information about the tags
     */
    protected $tagInfo;

    /**
     * @var array The path of the files which are converted
     */
    protected $files;

    /**
     * @var string The name of extensions
     */
    protected $extension;

    /**
     * @var array:\app\Converter The collection of converters
     */
    protected $converters= array();

    /**
     * @param string $path
     */
    public function __construct($extension="tpl", $path = null)
    {
        $this->files= array();
        $this->extension= $extension;
        $this->setConverters();
        if ($path != null) {
            $this->setTags($path);
        }
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
     * @return string
     */
    abstract protected function getTemplateCollectorRegex();

    /**
     * Converts from PHP.
     *
     * @param string $extension
     * @param string $fromPath Path name
     * @param string $toFileName File name
     * @return mixed
     */
    abstract public function convertFromPHP($extension, $fromPath, $toFileName);

    /**
     * Saves conversion to files
     */
    final public function saveConversion()
    {
        //Convert tags
        foreach ($this->getConvertedTags() as $key => $tag) {
            $info= $this->getTagInfo();
            foreach ($info[$key]["files"] as $file) {
                $text= file_get_contents($file);
                $text= str_replace($key, $tag, $text);
                file_put_contents($file, $text);
            }
        }
    }

    /**
     * @param string $toExtension
     */
    final public function renameFileExtensions($toExtension)
    {
        foreach ($this->files as $file) {
            $extension= pathinfo($file, PATHINFO_EXTENSION);
            echo $extension . " | " . substr_replace($file, $toExtension, -strlen($extension));
            rename($file, substr_replace($file, $toExtension, -strlen($extension)));
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
     * @param string $path
     */
    final public function setTags($path)
    {
        $this->getTemplateFiles($path);
        $this->tags = array();
        $this->tagInfo = array();

        $allTagCount= 0;
        $differentTagCount= 0;
        foreach ($this->files as $f) {
            $content = file_get_contents(realpath($f));
            $matches = array();
            if ($content != null && empty($content) === false) {
                $content = preg_replace("/\n\r/", "\n", $content);
                preg_match_all($this->getTemplateCollectorRegex(), $content, $matches);
                if ($matches != null && empty($matches[0]) === false) {
                    foreach ($matches[0] as $m) {
                        if (isset($this->tags[$m]) == false) {
                            $this->tags[$m]= $m;
                            $this->tagInfo[$m]= array("files" => array(), "count" => 0);
                            $differentTagCount++;
                        }
                        $this->tagInfo[$m]["files"][]= realpath($f);
                        $this->tagInfo[$m]["count"]++;
                        $allTagCount++;
                    }
                }
            }
        }
        echo "----------------------------------------------<br/>";
        echo "----------------------------------------------<br/>";
        echo "TAGS FETCHED:<br/>";
        echo "All: $allTagCount<br/>";
        echo "Different: $differentTagCount<br/><br/>";
        echo "---------------------------------------------<br/>";
    }

    /**
     * Az eddig már módosított template-eket adja vissza.
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

    /**
     * @param string $path
     */
    final protected function getTemplateFiles($path)
    {
        // Fájlnevek összegyűjtése a $files tömbbe
        $filesObject = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        // Fájllista szűrése csak .tpl kiterjesztésű fájlokra
        foreach ($filesObject as $name => $object) {
            if (pathinfo($name, PATHINFO_EXTENSION) == $this->getExtension()) {
                $this->files[] = $name;
            }
        }
        echo "FILES: " . count($this->files) . "<br/>";
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
     * Template-ek egy tömböt visszaadó PHP fájlba írása.
     * @param string $toFile
     * @param array $templates
     */
    final protected function writeTagsToPHPFile($toFile, array $templates)
    {
        $content = "<?php return array(\n";
        foreach ($templates as $k => $v) {
            $k = addslashes($k);
            $v = addslashes($v);
            $content .= "'" . $k . "' =>\n'" . $v . "',\n\n";
        }
        $content .= ");\n";
        if (is_dir($this->getTempDirectory()) != true) {
            mkdir($this->getTempDirectory());
        }
        file_put_contents(realpath($this->getTempDirectory()) . "/$toFile.php", $content);
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
        foreach ($this->converters as $converter) {
            if ($converter instanceof Converter) {
                $differentSum+= $converter->getConversionInfoDifferentSum();

                echo $converter->getName() . "<br/>";
                $converter->echoConversionInfo();
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
        echo "----------------------------------------------<br/>";
        echo "TAGS CONVERTED:<br/>";
        echo "All: $allSum<br/>";
        echo "Different: $differentSum<br/>";
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param array $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
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
}
