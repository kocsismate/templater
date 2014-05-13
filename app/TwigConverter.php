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
        $this->transform();
        $this->writeTemplatesToFile($toFileName."-converted", $this->getConvertedTemplates());
        $this->writeTemplatesToFile($toFileName."-remaining", $this->getRemainingTemplates());
    }

    protected function transform()
    {
        $transforms= array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
        foreach ($this->templates as &$t) {
            $transforms[0]+= $this->transformSimpleEcho($t);
            $transforms[1]+= $this->transformSimpleEchoWithoutEnd($t);
            $transforms[2]+= $this->transformSimpleIfOpen($t);
            $transforms[3]+= $this->transformSimpleIfClose($t);
            $transforms[4]+= $this->transformSimpleStrToLower($t);
            $transforms[5]+= $this->transformSimpleArrayEcho($t);
            $transforms[6]+= $this->transformSimpleStringLiteralIfOpen($t);
            $transforms[7]+= $this->transformSimpleArrayIfOpen($t);
            $transforms[8]+= $this->transformSimpleArrayVariableIfOpen($t);
            $transforms[9]+= $this->transformIntArrayEcho($t);
            $transforms[10]+= $this->transformEchoArrayCount($t);
            $transforms[11]+= $this->transformSimpleCountIntConditionIfOpen($t);
        }

        echo "SIMPLE ECHO TRANSFORMS: $transforms[0]<br/>";
        echo "SIMPLE ECHO TRANSFORMS WITHOUT END: $transforms[1]<br/>";
        echo "SIMPLE IF OPEN TRANSFORMS: $transforms[2]<br/>";
        echo "SIMPLE IF CLOSE TRANSFORMS: $transforms[3]<br/>";
        echo "SIMPLE STRTOLOWER FUNCTION TRANSFORMS: $transforms[4]<br/>";
        echo "SIMPLE ARRAY ECHO: $transforms[5]<br/>";
        echo "SIMPLE IF OPEN WITH STRING LITERAL: $transforms[6]<br/>";
        echo "SIMPLE IF ARRAY OPEN WITH STRING LITERAL: $transforms[7]<br/>";
        echo "SIMPLE IF ARRAY WITH VARIABLE: $transforms[8]<br/>";
        echo "SIMPLE IF ARRAY OPEN WITH INT LITERAL: $transforms[9]<br/>";
        echo "ARRAY ECHO COUNT: $transforms[10]<br/>";
        echo "SIMPLE COUNT WITH INT CONDITION: $transforms[11]<br/>";

        $sum= 0;
        foreach ($transforms as $t) {
            $sum+= $t;
        }
        echo "----------------------------------------------<br/>";
        echo "TOTAL: $sum<br/>";
    }

    /**
     * * Lecseréli a <?php echo $warning; ?> típusú template-eket.
     * @param string $template
     * @return int A változások száma
     */
    protected function transformSimpleEcho(&$template)
    {
        $count = 0;
        $template = preg_replace(
            "/".self::PHP_TEMPLATE_START .
            self::PHP_ECHO_BEGIN . self::PHP_CAPTURED_VARIABLE . self::PHP_STATEMENT_END .
            self::PHP_TEMPLATE_END."/s",
            "{{ \\1 }}",
            $template,
            -1,
            $count
        );

        return $count;
    }

    /**
     * * Lecseréli a <?php echo $warning ?> típusú template-eket.
     * @param string $template
     * @return int A változások száma
     */
    protected function transformSimpleEchoWithoutEnd(&$template)
    {
        $count = 0;
        $template = preg_replace(
            "/".self::PHP_TEMPLATE_START .
            self::PHP_ECHO_BEGIN . self::PHP_CAPTURED_VARIABLE .
            self::PHP_TEMPLATE_END."/s",
            "{{ \\1 }}",
            $template,
            -1,
            $count
        );

        return $count;
    }

    /**
     * Lecseréli a <?php if ($warning) ?> típusú template-eket.
     * @param string $template
     * @return int A változások száma
     */
    protected function transformSimpleIfOpen(&$template)
    {
        $count= 0;
        $template= preg_replace(
            "/" . self::PHP_TEMPLATE_START .
            self::PHP_IF_CONDITION_BEGIN . self::PHP_CAPTURED_VARIABLE . self::PHP_IF_CONDITION_END .
            self::PHP_TEMPLATE_END . "/s",
            "%{ if \\1 }%",
            $template,
            -1,
            $count
        );

        return $count;
    }

    /**
     * Lecseréli a <?php } ?> típusú template-eket.
     * @param string $template
     * @return int A változások száma
     */
    protected function transformSimpleIfClose(&$template)
    {
        $count= 0;
        $template= preg_replace(
            "/" . self::PHP_TEMPLATE_START .
            "\}" .
            self::PHP_TEMPLATE_END . "/s",
            "{% endif %}",
            $template,
            -1,
            $count
        );

        return $count;
    }

    /**
     * Lecseréli a <?php echo strtolower($warning); ?> típusú template-eket.
     * @param string $template
     * @return int A változások száma
     */
    protected function transformSimpleStrToLower(&$template)
    {
        $count = 0;
        $template = preg_replace(
            "/" . self::PHP_TEMPLATE_START .
            self::PHP_ECHO_BEGIN . "strtolower\(\s*" . self::PHP_CAPTURED_VARIABLE . "\s*\)" .
            self::PHP_STATEMENT_END .  self::PHP_TEMPLATE_END . "/s",
            "{{ \\1|lower }}",
            $template,
            -1,
            $count
        );

        return $count;
    }

    /**
     * * Lecseréli a <?php echo $warning["valami"]; ?> típusú template-eket.
     * @param string $template
     * @return int A változások száma
     */
    protected function transformSimpleArrayEcho(&$template)
    {
        $count = 0;
        $template = preg_replace(
            "/" . self::PHP_TEMPLATE_START .
            self::PHP_ECHO_BEGIN . self::PHP_CAPTURED_VARIABLE . self::PHP_CAPTURED_ARRAY_STRING_LITERAL .
            self::PHP_STATEMENT_END . self::PHP_TEMPLATE_END . "/s",
            "{{ \\1.\\2 }}",
            $template,
            -1,
            $count
        );

        return $count;
    }

    /**
     * Lecseréli a <?php if ($warning == "valami") ?> típusú template-eket.
     * @param string $template
     * @return int A változások száma
     */
    protected function transformSimpleStringLiteralIfOpen(&$template)
    {
        $count= 0;
        $template= preg_replace(
            "/" . self::PHP_TEMPLATE_START .
            self::PHP_IF_CONDITION_BEGIN . self::PHP_CAPTURED_VARIABLE . self::PHP_EQUALS_OPERATOR .
            self::PHP_STRING_LITERAL_START . "([A-Za-z0-9_\-\.]*)" . self::PHP_STRING_LITERAL_END .
            self::PHP_IF_CONDITION_END . self::PHP_TEMPLATE_END . "/s",
            "%{ if \\1 == '\\2' }%",
            $template,
            -1,
            $count
        );

        return $count;
    }

    /**
     * * Lecseréli a <?php if $warning["valami"] ?> típusú template-eket.
     * @param string $template
     * @return int A változások száma
     */
    protected function transformSimpleArrayIfOpen(&$template)
    {
        $count = 0;
        $template = preg_replace(
            "/" . self::PHP_TEMPLATE_START .
            self::PHP_IF_CONDITION_BEGIN . self::PHP_CAPTURED_VARIABLE . self::PHP_CAPTURED_ARRAY_STRING_LITERAL .
            self::PHP_IF_CONDITION_END . self::PHP_TEMPLATE_END . "/s",
            "%{ if \\1.\\2 }%",
            $template,
            -1,
            $count
        );

        return $count;
    }

    /**
     * Lecseréli a <?php if ($warning["valami"] == $valami) ?> típusú template-eket.
     * @param string $template
     * @return int A változások száma
     */
    protected function transformSimpleArrayVariableIfOpen(&$template)
    {
        $count= 0;
        $template= preg_replace(
            "/" . self::PHP_TEMPLATE_START .
            self::PHP_IF_CONDITION_BEGIN . self::PHP_CAPTURED_VARIABLE . self::PHP_CAPTURED_ARRAY_STRING_LITERAL .
            self::PHP_EQUALS_OPERATOR .
            self::PHP_CAPTURED_VARIABLE . self::PHP_IF_CONDITION_END . self::PHP_TEMPLATE_END . "/s",
            "%{ if \\1.\\2 == \\3 }%",
            $template,
            -1,
            $count
        );

        return $count;
    }

    /**
     * * Lecseréli a <?php echo $warning[0]; ?> típusú template-eket.
     * @param string $template
     * @return int A változások száma
     */
    protected function transformIntArrayEcho(&$template)
    {
        $count = 0;
        $template = preg_replace(
            "/" . self::PHP_TEMPLATE_START .
            self::PHP_ECHO_BEGIN . self::PHP_CAPTURED_VARIABLE . self::PHP_CAPTURED_ARRAY_INT_LITERAL .
            self::PHP_STATEMENT_END . self::PHP_TEMPLATE_END . "/s",
            "{{ \\1.\\2 }}",
            $template,
            -1,
            $count
        );

        return $count;
    }

    /**
     * Lecseréli a <?php echo count($warning["valami"]); ?> típusú template-eket.
     * @param string $template
     * @return int A változások száma
     */
    protected function transformEchoArrayCount(&$template)
    {
        $count = 0;
        $template = preg_replace(
            "/" . self::PHP_TEMPLATE_START .
            self::PHP_ECHO_BEGIN . "count" . self::PHP_FUNCTION_OPENING_BRACKET .
            self::PHP_CAPTURED_VARIABLE . self::PHP_CAPTURED_ARRAY_STRING_LITERAL .
            self::PHP_FUNCTION_CLOSING_BRACKET . self::PHP_STATEMENT_END .  self::PHP_TEMPLATE_END . "/s",
            "{{ \\1|length }}",
            $template,
            -1,
            $count
        );

        return $count;
    }

    /**
     * Lecseréli a <?php if count($warning)>0 ?> típusú template-eket.
     * @param string $template
     * @return int A változások száma
     */
    protected function transformSimpleCountIntConditionIfOpen(&$template)
    {
        $count = 0;
        $template = preg_replace(
            "/" . self::PHP_TEMPLATE_START .
            self::PHP_IF_CONDITION_BEGIN . "count" . self::PHP_FUNCTION_OPENING_BRACKET .
            self::PHP_CAPTURED_VARIABLE . self::PHP_FUNCTION_CLOSING_BRACKET .
            self::PHP_CAPTURED_OPERATOR . "(" . self::PHP_INT_LITERAL . ")" . self::PHP_IF_CONDITION_END .
            self::PHP_TEMPLATE_END . "/s",
            "%{ if \\1|length \\2 \\3  }%",
            $template,
            -1,
            $count
        );

        return $count;
    }

















    //---------------------------------------------------------------------------------------


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
        echo "NUM OF TEMPLATES: ".count($this->templates)."<br/>----------------------------------------------<br/>";
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
     * Az eddig már módosított template-eket adja vissza.
     * @return array
     */
    protected function getConvertedTemplates()
    {
        $result= array();

        foreach ($this->templates as $k => $v) {
            if ($k != $v) {
                $result[$k]= $v;
            }
        }

        return $result;
    }

    /**
     * Az eddig még nem módosított template-eket adja vissza.
     * @return array
     */
    protected function getRemainingTemplates()
    {
        $result= array();

        foreach ($this->templates as $k => $v) {
            if ($k == $v) {
                $result[$k]= $v;
            }
        }

        return $result;
    }

    /**
     * Template-ek txt fájlba írása.
     * @param string $toFile
     * @param array $templates
     */
    protected function writeTemplatesToFile($toFile, array $templates)
    {
        $content= "";
        foreach ($templates as $k => $v) {
            $content.= "$k\n\n$v\n----------------------------------------------------------------------------------\n";
        }
        if (is_dir(__DIR__ . "/../temp") != true) {
            mkdir(__DIR__ . "/../temp");
        }
        file_put_contents(realpath(__DIR__ . "/../temp") . "/$toFile.txt", $content);
    }

    /**
     * Template-ek egy tömböt visszaadó PHP fájlba írása.
     * @param string $toFile
     * @param array $templates
     */
    protected function writeTemplatesToPHPFile($toFile, array $templates)
    {
        $content = "<?php return array(\n";
        foreach ($templates as $k => $v) {
            $k = addslashes($k);
            $v = addslashes($v);
            $content.= "'" . $k . "' =>\n'" . $v . "',\n\n";
        }
        $content .= ");\n";
        if (is_dir(__DIR__ . "/../temp") != true) {
            mkdir(__DIR__ . "/../temp");
        }
        file_put_contents(realpath(__DIR__ . "/../temp") . "/$toFile.php", $content);
    }
}
