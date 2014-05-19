<?php
namespace app\php;

use app\Template;

/**
 * Az osztály rövid leírása
 *
 * Az osztály hosszú leírása, példakód
 * akár több sorban is
 *
 * @package
 * @author kocsismate
 * @since 2014.05.13. 12:47
 */
final class PHPTemplate extends Template
{
    const TEMPLATE_START = "<\?php\s+";
    const TEMPLATE_END = "\s*(?:\?>|\/\/end)";

    const CAPTURED_OPTIONAL_ARGUMENT_SEPARATOR = "\s*([,]{0,1})\s*";
    const OPTIONAL_STATEMENT_END = "\s*;*";

    const PRINTF_BEGIN = "printf\s*\(\s*";
    const PRINTF_END = "\s*\)\s*";
    const ECHO_BEGIN = "echo\s*[\(]{0,1}\s*";
    const ECHO_OPTIONAL_END = "\s*[\)]{0,1}\s*";

    const IF_CONDITION_BEGIN = "if\s*\(\s*";
    const ELSE_IF_CONDITION_BEGIN = "\s*[\}]{0,1}\s*(?:elseif|else if)\s*\(\s*";
    const ELSE_BEGIN = "\s*[\}]{0,1}\s*else\s*[\{\:]*\s*";
    const ENDIF_STATEMENT = "\s*[\}]{0,1}\s*endif\s*[;]{0,1}\s*";
    const IF_CONDITION_END = "\s*\)\s*[\{\:]{0,1}";

    const FOREACH_HEAD_BEGIN = "foreach\s*\(\s*";
    const FOREACH_HEAD_END = "\s*\)\s*[\{\:]*\s*";
    const ENDFOREACH_STATEMENT = "\s*[\}]{0,1}\s*endforeach\s*[;]{0,1}\s*";

    /**
     * @see \app\Template::getExtension()
     */
    protected function getExtension()
    {
        return "tpl";
    }

    /**
     * @return string
     */
    protected function getTemplateCollectorRegex()
    {
        return "/" . self::TEMPLATE_START . ".*?" . self::TEMPLATE_END . "/s";
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
    public function convertFromPHP($fromPath, $toFileName)
    {
        throw new \Exception("");
    }
}
