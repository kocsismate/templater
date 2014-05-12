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
class TwigConverter extends Converter
{
    /**
     * @var array The collection of templates
     */
    private $templates;

    /**
     * @see \app\Converter::convertFromPHP()
     */
    public function convertFromPHP($fromDir, $toFileName)
    {
        $files= $this->getPHPTemplateFiles($fromDir);
        $this->createTemplateCollectionFromPHP($files);
        $this->writeTemplatesToFile($toFileName);
        $this->transform();
        $this->writeTemplatesToFile($toFileName);
    }

    protected function transform()
    {
        echo "/" . self::PHP_TEMPLATE_START .
            "(echo \$([A-Za-z0-9_]+)\s*;)" .
            self::PHP_TEMPLATE_END . "/s",
        "\{\{ \\2 \}\}";

        $c = 0;
        foreach ($this->templates as &$t) {
            $c+= $this->transformSimpleEcho($t);
        }

        echo "SIMPLE ECHO TRANSFORMS: $c<br/>";
    }

    /**
     * @param string $template
     * @return int A változások száma
     */
    protected function transformSimpleEcho(&$template)
    {
        $count= 0;
        $template= preg_replace(
            "/" . self::PHP_TEMPLATE_START .
            "(echo \\$([A-Za-z0-9_]+)\s*;)" .
            self::PHP_TEMPLATE_END . "/s",
            "{{ a }}",
            $template,
            -1,
            $count
        );

        return $count;
    }

    /**
     *  Elkészíti a template-ek gyűjteményét egy tömböt visszaadó fájlba, amely a temp mappában foglal helyet.
     * @param array $files
     */
    protected function createTemplateCollectionFromPHP($files)
    {
        // PHP template-ek összegyűjtése a .tpl fájlokból a $templates tömbbe
        $this->templates = array();
        foreach ($files as $f) {
            $content = file_get_contents(realpath($f));
            $matches = array();
            if ($content != null && empty($content) === false) {
                $content = preg_replace("/\n\r/", "\n", $content);
                preg_match_all("/" . self::PHP_TEMPLATE_START . ".*?" . self::PHP_TEMPLATE_END . "/s", $content, $matches);
                if ($matches != null && empty($matches[0]) === false) {
                    foreach ($matches[0] as $m) {
                        $this->templates[$m] = $m;
                    }
                }
            }
        }
        echo "NUM OF TEMPLATES: ".count($this->templates)."<br/>";
    }

    /**
     * @param string $path
     * @return array Array of files
     */
    protected function getPHPTemplateFiles($path)
    {
        // Fájlnevek összegyűjtése a $files tömbbe
        $filesObject = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        // Fájllista szűrése csak .tpl kiterjesztésű fájlokra
        $files = array();
        foreach ($filesObject as $name => $object) {
            if (pathinfo($name, PATHINFO_EXTENSION) == "tpl") {
                $files[] = $name;
            }
        }
        echo "NUM OF FILES: ".count($files)."<br/>";

        return $files;
    }

    /**
     * Template-ek fájlba írása
     * @param string $toFile
     */
    protected function writeTemplatesToFile($toFile)
    {
        $content = "<?php return array(\n";
        foreach ($this->templates as $k => $v) {
            $k = addslashes($k);
            $v = addslashes($v);
            $content .= "'" . $k . "' =>\n'" . $v . "',\n\n";
        }
        $content .= ");\n";
        if (is_dir(__DIR__ . "/../temp") != true) {
            mkdir(__DIR__ . "/../temp");
        }
        file_put_contents(realpath(__DIR__ . "/../temp") . "/$toFile.php", $content);
    }
}
