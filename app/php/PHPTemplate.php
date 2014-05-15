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
    const CAPTURED_ARRAY_INT_INDEX= "\s*\[\s*([0-9]+)\]";
    const CAPTURED_ARRAY_STRING_INDEX= "\s*\[(\s*[\'\"][A-Za-z0-9_\-\.]+[\'\"])\s*\]";
    const CAPTURED_ARRAY_VARIABLE_INDEX= "\s*\[\s*(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\]";

    const IDENTIFIER= "[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*";
    const ARRAY_INT_INDEX= "\s*\[\s*[0-9]+\]";
    const ARRAY_STRING_INDEX= "\s*\[\s*[\'\"][A-Za-z0-9_\-\.]+[\'\"]\s*\]";
    const ARRAY_VARIABLE_INDEX= "\s*\[\s*\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\]";
    const OBJECT_REFERENCE= "\s*(?:->|\:\:)\s*";

    const CAPTURED_VALID_IDENTIFIER= "[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*";
    const CAPTURED_VARIABLE_NAME= "\\$([A-Za-z0-9_]+)";
    const CAPTURED_VARIABLE= "(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";
    const FUNCTION_OPENING_BRACKET= "\s*\(\s*";
    const FUNCTION_CLOSING_BRACKET= "\s*\)\s*";
    const STATEMENT_END= "\s*;";
    const OPTIONAL_STATEMENT_END= "\s*;*";

    const PRINTF_BEGIN= "printf\s*\(\s*";
    const PRINTF_END= "\s*\)\s*";
    const ECHO_BEGIN= "echo\s*[\(]{0,1}\s*";
    const ECHO_OPTIONAL_END= "\s*[\)]{0,1}\s*";

    const IF_CONDITION_BEGIN= "if\s*\(\s*";
    const IF_CONDITION_END= "\)\s*[{:]*";

    const FOREACH_HEAD_BEGIN= "foreach\s*\(\s*";
    const FOREACH_HEAD_CAPTURED_VALUE= "\s*as\s*\\$([A-Za-z0-9_]+)";
    const FOREACH_HEAD_CAPTURED_KEY_AND_VALUE= "\s*as\s*\\$([A-Za-z0-9_]+)\s*=>\s*\\$([A-Za-z0-9_]+)";
    const FOREACH_HEAD_END= "\s*\)\s*[\{\:]*\s*";

    const CAPTURED_OPERATOR= "\s*(\+|\-|\*|\/|\%|\.)\s*";
    const CAPTURED_COMPARATOR= "\s*(==|>|>=|<|<=|!=)\s*";
    const CAPTURED_OPTIONAL_UNARY_OPERATOR= "\s*([!]{0,1})\s*";
    const CAPTURED_LOGICAL_BINARY_OPERATOR= "\s*(&&|\|\|)\s*";
    const EQUALS_OPERATOR= "\s*==\s*";

    const CAPTURED_BOOLEAN_LITERAL= "\s*(true|false)\s*";
    const CAPTURED_INT_LITERAL= "\s*([0-9])+\s*";
    const CAPTURED_STRING_LITERAL= "\s*(\'[^\']*\'|\"[^\"]*\")\s*";
    const STRING_LITERAL_START= "\s*[\'\"]";
    const STRING_LITERAL_END= "\s*[\'\"]";

    const CAPTURED_FUNCTION_NAME= "\s*(isset|empty|trim|strtolower|count|is_array)\s*"; // is defined is empty, |lower, |length, is iterable
    const CAPTURED_OPTIONAL_ARGUMENT_SEPARATOR= "\s*([,]{0,1})\s*";
    
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
