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
     * @see \app\Converter::convertFromPHP()
     */
    public function convertFromPHP($fromDir, $toFileName)
    {
        $this->createTemplateCollectionFromPHP($fromDir, $toFileName);
    }

    /**
     *  Elkészíti a temp mappába a template-ek gyűjteményét egy tömböt visszaadó fájlba.
     * @param string $fromDir
     * @param string $toFile
     */
    protected function createTemplateCollectionFromPHP($fromDir, $toFile)
    {
        // Fájlnevek összegyűjtése a $files tömbbe
        $filesObject = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($fromDir),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $files= array();
        foreach ($filesObject as $name => $object) {
            if (pathinfo($name, PATHINFO_EXTENSION) == "tpl") {
                $files[]= $name;
            }
        }

        // PHP template-ek összegyűjtése a .tpl fájlokból a $templates tömbbe
        $templates= array();
        $c= 0;
        foreach ($files as $f) {
            $content= file_get_contents(realpath($f));
            $matches= array();
            if ($content != null && empty($content) === false) {
                $content= preg_replace("/\n\r/", "\n", $content);
                preg_match_all("/<\?php.*?(\?>|\/\/end)/s", $content, $matches);
                if ($matches != null && empty($matches[0]) === false) {
                    foreach ($matches[0] as $m) {
                        //echo htmlspecialchars($m) . "<br/>";
                        $templates[$m] = $m;
                    }
                }
            }

            $c++;
        }
        echo "<br/>-----------------------------<br/>NUM OF FILES: $c<br/>";

        // Template-ek fájlba írása
        $content= "<?php return array(\n";
        $c= 0;
        foreach ($templates as $t) {
            $t= addslashes($t);
            $content.= "'".$t."' =>\n'".$t."',\n\n";
            $c++;
        }
        $content.= ");\n";
        if (is_dir(__DIR__."/../temp") != true) {
            mkdir(__DIR__."/../temp");
        }
        file_put_contents(realpath(__DIR__."/../temp")."/$toFile.php", $content);

        echo "<br/>-----------------------------<br/>NUM OF TEMPLATES: $c<br/>";
    }
}
