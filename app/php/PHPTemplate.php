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
    const TEMPLATE_START= "<\?php\s+";
    const TEMPLATE_END= "\s*(\?>|\/\/end)";

    const CAPTURED_IDENTIFIER= "([A-Za-z0-9_]+)";
    const CAPTURED_VARIABLE= "\\$([A-Za-z0-9_]+)";
    const OBJECT_REFERENCE= "\s*->\s*";
    const FUNCTION_OPENING_BRACKET= "\s*\(\s*";
    const FUNCTION_CLOSING_BRACKET= "\s*\)\s*";
    const STATEMENT_END= "\s*;";
    const OPTIONAL_STATEMENT_END= "\s*;*";

    const ECHO_BEGIN= "echo\s*";

    const IF_CONDITION_BEGIN= "if\s*\(\s*";
    const IF_CONDITION_END= "\)\s*[{]*";

    const FOREACH_HEAD_BEGIN= "foreach\s*\(\s*";
    const FOREACH_HEAD_CAPTURED_VALUE= "\s*as\s*\\$([A-Za-z0-9_]+)";
    const FOREACH_HEAD_CAPTURED_KEY_AND_VALUE= "\s*as\s*\\$([A-Za-z0-9_]+)\s*=>\s*\\$([A-Za-z0-9_]+)";
    const FOREACH_HEAD_END= "\)\s*[{]*";

    const CAPTURED_OPERATOR= "\s*(==|>|>=|<|<=|!=)\s*";
    const EQUALS_OPERATOR= "\s*==\s*";

    const INT_LITERAL= "\s*[0-9]+\s*";
    const STRING_LITERAL_START= "\s*[\'\"]";
    const STRING_LITERAL_END= "\s*[\'\"]";

    const CAPTURED_ARRAY_INT_INDEX= "\s*\[\s*([0-9]+)\]";
    const CAPTURED_ARRAY_STRING_INDEX= "\s*\[\s*[\'\"]([A-Za-z0-9_\-\.]+)[\'\"]\s*\]";
    const CAPTURED_ARRAY_VARIABLE_INDEX= "\s*\[\s*\\$([A-Za-z0-9_]+)\]";
    
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
