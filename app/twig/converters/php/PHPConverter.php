<?php
namespace app\twig\converters\php;

/**
 * Az osztály rövid leírása
 *
 * Az osztály hosszú leírása, példakód
 * akár több sorban is
 *
 * @package
 * @author kocsismate
 * @since 2014.05.14. 11:04
 */
class PHPConverter
{
    public static function getOperatorList()
    {
        return array("+", "-", "*", "/", "%", ".");
    }

    public static function getComparatorList()
    {
        return array("<", "<=", "!=", "==", ">=", ">");
    }

    public static function getLogicalBinaryOperatorList()
    {
        return array("\&\&", "\|\|");
    }

    public static function getLogicalUnaryOperatorList()
    {
        return array("!");
    }

    public static function getFunctionNameList()
    {
        return array("isset", "empty", "trim", "strtolower", "count", "is_array", "date", "printf", "strip_tags");
    }

    public static function getTemplateStartRegex()
    {
        return "<\?php\s+";
    }

    public static function getTemplateEndRegex()
    {
        return "\s*(?:\?>|\/\/end)";
    }

    public static function getOpeningBracketRegex()
    {
        return "\s*\(\s*";
    }

    public static function getClosingBracketRegex($isCaptured = false)
    {
        $pattern = "\s*\)\s*";
        if ($isCaptured == true) {
            $pattern = "($pattern)";
        }

        return $pattern;
    }

    public static function getObjectReferenceRegex()
    {
        return "\s*(?:->|\:\:)\s*";
    }

    public static function getStatementEndRegex($isOptional = true)
    {
        return "\s*;" . ($isOptional == true ? "*" : "");
    }

    public static function getOperatorRegex($isCaptured = false)
    {
        $pattern = "\s*(" . ($isCaptured == true ? "" : "?:");
        $list = self::getOperatorList();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            if ($i != 0) {
                $pattern .= "|";
            }
            $pattern .= "\\" . $list[$i];
        }
        $pattern .= ")\s*";

        return $pattern;
    }

    public static function getComparatorRegex($isCaptured = false)
    {
        $pattern = "\s*(" . ($isCaptured == true ? "" : "?:");
        $list = self::getComparatorList();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            if ($i != 0) {
                $pattern .= "|";
            }
            $pattern .= "\\" . $list[$i];
        }
        $pattern .= ")\s*";

        return $pattern;
    }

    public static function getLogicalBinaryOperatorRegex($isCaptured = false)
    {
        $pattern = "\s*(" . ($isCaptured == true ? "" : "?:");
        $list = self::getLogicalBinaryOperatorList();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            if ($i != 0) {
                $pattern .= "|";
            }
            $pattern .= $list[$i];
        }
        $pattern .= ")\s*";

        return $pattern;
    }

    public static function getLogicalUnaryOperatorRegex($isOptional = true, $isCaptured = false)
    {
        $pattern = "(" . ($isCaptured == true ? "" : "?:");

        $list = self::getLogicalUnaryOperatorList();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            if ($i != 0) {
                $pattern .= "|";
            }
            $pattern .= $list[$i];
        }
        $pattern .= ")";
        if ($isOptional == true) {
            $pattern .= "{0,1}";
        }

        $pattern .= "\s*";

        return $pattern;
    }

    public static function getFunctionNameRegex($isCaptured = false)
    {
        $pattern = "(" . ($isCaptured == true ? "" : "?:");
        $list = self::getFunctionNameList();
        $count = count($list);
        for ($i = 0; $i < $count; $i++) {
            if ($i != 0) {
                $pattern .= "|";
            }
            $pattern .= $list[$i];
        }
        $pattern .= ")";

        return $pattern;
    }

    public static function getArgumentSeparatorRegex()
    {
        return "\s*[,]\s*";
    }

    public static function getBooleanLiteralRegex($isCaptured = false)
    {
        return "(" . ($isCaptured == true ? "" : "?:") . "true|false|TRUE|FALSE)";
    }

    public static function getIntLiteralRegex($isCaptured = false)
    {
        $pattern = "[0-9]+";
        if ($isCaptured == true) {
            $pattern = "(" . $pattern . ")";
        }

        return $pattern;
    }

    public static function getStringLiteralRegex($isCaptured = false)
    {
        return "(" . ($isCaptured == true ? "" : "?:") . "\'[^\']*\'|\"[^\"\$]*\")";
    }

    public static function getIdentifierRegex($isCaptured = false)
    {
        $pattern = "(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";
        if ($isCaptured == true) {
            $pattern = "($pattern)";
        }

        return $pattern;
    }

    public static function getVariableRegex($isCaptured = false)
    {
        $pattern = "\\$(?!_SESSION|_REQUEST|_GET|_POST|_GLOBAL)" . self::getIdentifierRegex(false);
        if ($isCaptured == true) {
            $pattern = "($pattern)";
        }

        return "$pattern";
    }

    public static function getArrayIntIndexRegex($isCaptured = false)
    {
        return "\s*\[\s*" . self::getIntLiteralRegex($isCaptured) . "\s*\]";
    }

    public static function getArrayStringIndexRegex($isCaptured = false)
    {
        return "\s*\[\s*" . self::getStringLiteralRegex($isCaptured) . "\s*\]";
    }

    public static function getArrayVariableIndexRegex($isCaptured = false)
    {
        return "\s*\[\s*" . self::getVariableRegex($isCaptured) . "\s*\]";
    }

    /**
     * Returns the regex for matching an expression with ternary operator.
     * @param boolean $isCaptured
     * @return string
     */
    public static function getTernaryOperatorExpressionRegex($isCaptured = false)
    {
        return
            "(" . ($isCaptured == true ? "" : "?:") .
            "\({0,1}" . self::getConditionalExpressionRegex(false) . "\){0,1}" .
            "\s*\?\s*" . self::getExpressionRegex(false) . "\s*\:\s*" . self::getExpressionRegex(false) .
            ")";
    }

    /**
     * @param boolean $isCaptured
     * @return string
     */
    public static function getConditionalExpressionRegex($isCaptured = false)
    {
        return
            "(" . ($isCaptured == true ? "" : "?:") .
                self::getConditionRegex(false) .
                "(?:" .
                    self::getLogicalBinaryOperatorRegex(false) .
                    self::getConditionRegex(false) .
                ")*" .
            ")";
    }

    /**
     * @param boolean $isCaptured
     * @return string
     */
    public static function getConditionRegex($isCaptured = false)
    {
        return
            "(" . ($isCaptured == true ? "" : "?:") .
                self::getExpressionRegex(false) .
                "(?:" .
                    self::getComparatorRegex(false) .
                    self::getExpressionRegex(false) .
                "){0,1}" .
            ")";
    }

    /**
     * Returns a regex for matching a simple expression.
     * @param boolean $isCaptured
     * @return string
     */
    public static function getExpressionRegex($isCaptured = false)
    {
        return
            "(" . ($isCaptured == true ? "" : "?:") .
                self::getPrimitiveCallRegex(false) .
                "(?:" .
                    self::getOperatorRegex(false) .
                    self::getPrimitiveCallRegex(false) .
                ")*" .
            ")";
    }

    /**
     * Returns a regex for matching a function or a variable call. No recursion allowed.
     * @param boolean $isCaptured
     * @return string
     */
    public static function getPrimitiveCallRegex($isCaptured = false)
    {
        return
            "(" . ($isCaptured == true ? "" : "?:") .
                self::getFunctionCallRegex(false) . "|" .
                self::getVariableCallRegex(false) . "|" .
                self::getBooleanLiteralRegex(false) . "|" .
                self::getIntLiteralRegex(false) . "|" .
                self::getStringLiteralRegex(false) .
            ")";
    }

    /**
     * Returns a regex for matching a function call with zero, one or two arguments.
     * @param boolean $isCaptured
     * @return string
     */
    public static function getFunctionCallRegex($isCaptured = false)
    {
        return
            self::getLogicalUnaryOperatorRegex(true, $isCaptured) .
            self::getFunctionNameRegex($isCaptured) .
            self::getOpeningBracketRegex(false) .
            "(" . ($isCaptured == true ? "" : "?:") . "(?:" .
            self::getArgumentSeparatorRegex(false) . "|" .
            self::getStaticCallRegex(false) . "|" .
            ")*)" .
            self::getClosingBracketRegex(false);
    }

    /**
     * Returns a regex for matching a function call with zero, one or two arguments.
     * @param boolean $isCaptured
     * @return string
     */
    public static function getStaticCallRegex($isCaptured = false)
    {
        return
            "(" . ($isCaptured == true ? "" : "?:") .
                self::getArgumentSeparatorRegex(false) . "|" .
                self::getVariableCallRegex(false) . "|" .
                self::getBooleanLiteralRegex(false) . "|" .
                self::getIntLiteralRegex(false) . "|" .
                self::getStringLiteralRegex(false) .
            ")";
    }

    /**
     * @param boolean $isCaptured
     * @return string
     */
    public static function getVariableCallRegex($isCaptured = false)
    {
        return
            self::getLogicalUnaryOperatorRegex(true, $isCaptured) .
            self::getVariableRegex($isCaptured) . // Variable
            "(" . ($isCaptured == true ? "" : "?:") .
                self::getVariableIndexRegex(true, false) .
            ")";
    }

    /**
     * @param boolean $isOptional
     * @param boolean $isCaptured
     * @return string
     */
    public static function getVariableIndexRegex($isOptional = true, $isCaptured = false)
    {
        return
            "(" . ($isCaptured == true ? "" : "?:") .
                self::getArrayIntIndexRegex(false) . "|" .                                      // Int array index
                self::getArrayStringIndexRegex(false) . "|" .                                   // String array index
                self::getArrayVariableIndexRegex(false) . "|" .                                 // Variable array index
                self::getObjectReferenceRegex(false) . self::getIdentifierRegex(false)."\(\)|". // Method
                self::getObjectReferenceRegex(false) . self::getIdentifierRegex(false) .        // Attribute
            ")" . ($isOptional == true? "*" : "");
    }

    //-----------------------------------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------------------------------

    /**
     * Converts a PHP expression to Twig format.
     * @param array $matches
     * @param int $from
     * @return string
     */
    public static function convertTernaryOperatorExpression(array $matches, &$from)
    {
        // The first match is the expression
        $expression = $matches[$from];

        // The conditional expression before the ?
        preg_match(
            "/".self::getConditionalExpressionRegex(true)."/",
            $expression,
            $operandMatches,
            PREG_OFFSET_CAPTURE
        );
        $operands[]= $operandMatches[1][0];
        // The first expression
        $expression= substr($expression, $operandMatches[0][1] + strlen($operandMatches[0][0]));
        preg_match("/".self::getExpressionRegex(true)."/", $expression, $operandMatches, PREG_OFFSET_CAPTURE);
        $operands[]= $operandMatches[1][0];

        // The second expression
        $expression= substr($expression, $operandMatches[0][1] + strlen($operandMatches[0][0]));
        preg_match("/".self::getExpressionRegex(true)."/", $expression, $operandMatches, PREG_OFFSET_CAPTURE);
        $operands[]= $operandMatches[1][0];

        $num= 0;
        $result = self::convertConditionalExpression($operands, $num);
        $result.= " ? ";
        $num++;
        $result.= self::convertExpression($operands, $num);
        $result.= " : ";
        $num++;
        $result.= self::convertExpression($operands, $num);

        return $result;
    }

    /**
     * Converts a PHP conditional expression to a Twig conditional expression.
     * @param array $matches
     * @param int $from
     * @return string
     */
    public static function convertConditionalExpression(array $matches, &$from)
    {
        // The first match is all the operands of the expression
        $condition = $matches[$from];
        preg_match_all(
            "/".self::getConditionRegex(true)."/",
            $condition,
            $operandMatches,
            PREG_PATTERN_ORDER|PREG_OFFSET_CAPTURE
        );
        $operandMatches= $operandMatches[1];

        $operands= array();
        $operators= array();
        $count= count($operandMatches);
        for ($i= 0; $i < $count; $i++) {
            $operands[]= $operandMatches[$i][0];
            if ($i + 1 < $count) {
                $start= $operandMatches[$i][1] + strlen($operandMatches[$i][0]);
                $operators[]= trim(substr($condition, $start, $operandMatches[$i+1][1] - $start));
            }
        }

        $result = "";
        $count = count($operands);
        for ($i = 0; $i < $count; $i++) {
            preg_match("/" . self::getConditionRegex(true) . "/", $operands[$i], $operandMatches);
            $operandFrom = 1;
            $result.= self::convertCondition($operandMatches, $operandFrom);
            if ($i + 1 < $count) {
                $result.= self::convertLogicalOperator($operators[$i]);
            }
        }

        return $result;
    }

    /**
     * @param string $operator
     * @return string
     */
    private static function convertLogicalOperator($operator)
    {
        $operators = array("&&" => " and ", "||" => " or ");
        return in_array($operator, array_keys($operators)) ? $operators[$operator] : $operator;
    }

    /**
     * Converts an elementary PHP condition to Twig format.
     * @param array $matches
     * @param int $from
     * @return string
     */
    public static function convertCondition(array $matches, $from)
    {
        // The first match is all the operands of the expression
        $condition = trim($matches[$from]);
        preg_match_all(
            "/".self::getExpressionRegex(true)."/",
            $condition,
            $operandMatches,
            PREG_PATTERN_ORDER|PREG_OFFSET_CAPTURE
        );
        $operandMatches= $operandMatches[1];

        $operands= array();
        $operators= array();
        $count= count($operandMatches);
        for ($i= 0; $i < $count; $i++) {
            $operands[]= $operandMatches[$i][0];
            if ($i + 1 < $count) {
                $start= $operandMatches[$i][1] + strlen($operandMatches[$i][0]);
                $operators[]= trim(substr($condition, $start, $operandMatches[$i+1][1] - $start));
            }
        }

        $result = "";
        $count = count($operands);
        for ($i = 0; $i < $count; $i++) {
            preg_match("/" . self::getExpressionRegex(true) . "/", $operands[$i], $operandMatches);
            $operandFrom = 1;
            $result .= self::convertExpression($operandMatches, $operandFrom);

            if ($i + 1 < $count) {
                $result .= (" " . $operators[$i] . " ");
            }
        }

        return $result;
    }

    /**
     * Converts a PHP expression to Twig format.
     * @param array $matches
     * @param int $from
     * @return string
     */
    public static function convertExpression(array $matches, &$from)
    {
        // The first match is all the operands of the expression
        $expression = $matches[$from];
        preg_match_all(
            "/".self::getPrimitiveCallRegex(true)."/",
            $expression,
            $operandMatches,
            PREG_PATTERN_ORDER|PREG_OFFSET_CAPTURE
        );
        $operandMatches= $operandMatches[1];

        $operands= array();
        $operators= array();
        $count= count($operandMatches);
        for ($i= 0; $i < $count; $i++) {
            $operands[]= $operandMatches[$i][0];
            if ($i + 1 < $count) {
                $start= $operandMatches[$i][1] + strlen($operandMatches[$i][0]);
                $operators[]= trim(substr($expression, $start, $operandMatches[$i+1][1] - $start));
            }
        }

        $result = "";
        $count = count($operands);
        for ($i = 0; $i < $count; $i++) {
            preg_match("/" . self::getPrimitiveCallRegex(true) . "/", $operands[$i], $operandMatches);
            $operandFrom = 1;
            $result .= self::convertPrimitiveCall($operandMatches, $operandFrom);

            if ($i + 1 < $count) {
                $result.= self::convertOperator($operators[$i]);
            }
        }

        return $result;
    }

    /**
     * @param string $operator
     * @return string
     */
    private static function convertOperator($operator)
    {
        $operators = array("+" => " + ", "-" => " - ", "*" => " * ", "/" => " / ", "%" => " % ", "." => " ~ ");
        return in_array($operator, array_keys($operators)) ? $operators[$operator] : $operator;
    }

    /**
     * Converts a PHP primitive expression to Twig format.
     * @param array $matches
     * @param int $from
     * @return string
     */
    public static function convertPrimitiveCall(array $matches, &$from)
    {
        $p = trim($matches[$from]);
        $q= self::convertStaticCall($matches, $from);

        if ($p == $q) {
            // Function call
            foreach (self::getFunctionNameList() as $f) {
                $pos = strpos($p, $f);
                if ($pos === 0 || ($pos === 1 && strpos($p, "!") === 0)) {
                    preg_match("/" . self::getFunctionCallRegex(true) . "/", $p, $functionMatches);
                    $functionFrom = 1;
                    $q= self::convertFunctionCall($functionMatches, $functionFrom);
                }
            }
        }

        return $q;
    }

    /**
     * Converts a PHP function call to a Twig function call.
     * @param array $matches
     * @param int $from
     * @return string
     */
    public static function convertFunctionCall(array $matches, &$from)
    {
        // The first possible match is the negation
        $isNegated = $matches[$from++] == "!";
        // The second match is the function name
        $function = $matches[$from++];
        // The next match is the arguments
        $allArguments = $matches[$from];
        preg_match_all("/".self::getStaticCallRegex(true)."/", $allArguments, $argumentMatches);
        $argumentMatches= $argumentMatches[1];

        $arguments = array();
        $count = count($argumentMatches);
        for ($i = 0; $i < $count; $i++) {
            $arguments[]= self::convertStaticCall($argumentMatches, $i);
        }

        $result = "";
        switch ($function) {
            case "printf":
                $result = $arguments[0] . "|format(";
                $count= count($arguments);
                for ($i= 1; $i < $count; $i++) {
                    if ($i > 1) {
                        $result.= ", ";
                    }
                    $result.= $arguments[$i];
                }
                $result.= ")";
                break;
            case "date":
                $result = (isset($arguments[1])? $arguments[1] : "\"now\"") . "|date($arguments[0])";
                if ($isNegated == true) {
                    $result = self::convertNegated($result);
                }
                break;
            case "isset":
                $result = $arguments[0] . " is " . ($isNegated == true? "not " : "") . "defined";
                break;
            case "empty":
                $result = $arguments[0] . " is ".($isNegated? "not " : "")."empty";
                break;
            case "trim":
                $result = $arguments[0] . "|trim";
                if (isset($arguments[1])) {
                    $result .= "('" . $arguments[1] . "')";
                }
                break;
            case "strtolower":
                $result = $arguments[0] . "|lower";
                if ($isNegated == true) {
                    $result = self::convertNegated($result);
                }
                break;
            case "count":
                $result = $arguments[0] . "|length";
                if ($isNegated == true) {
                    $result = self::convertNegated($result);
                }
                break;
            case "is_array":
                $result = $arguments[0] . " is ".($isNegated? "not " : "")."iterable";
                break;
            case "strip_tags":                                  // TODO Only supports one argument
                $result = $arguments[0] . "|striptags";
                break;
        }

        return $result;
    }

    /**
     * Converts a PHP primitive expression to Twig format.
     * @param array $matches
     * @param int $from
     * @return string
     */
    public static function convertStaticCall(array $matches, &$from)
    {
        $staticCall = trim($matches[$from]);

        // String literal
        if (strpos($staticCall, '"') === 0 || strpos($staticCall, "'") === 0) {
            return $staticCall;
        }

        // Int literal
        if (is_numeric($staticCall)) {
            return self::convertInt($staticCall);
        }

        // Boolean literal
        if (is_bool($staticCall)) {
            return self::convertBoolean($staticCall);
        }

        // Variable call
        $pos = strpos($staticCall, "$");
        if ($pos === 0 || ($pos === 1 && strpos($staticCall, "!") === 0)) {
            preg_match("/" . self::getVariableCallRegex(true) . "/", $staticCall, $variableMatches);
            $variableFrom = 1;
            return self::convertVariableCall($variableMatches, $variableFrom);
        }

        return $staticCall;
    }

    /**
     * Converts a PHP variable call (which can be a regular variable, an array index call or an object attribute call)
     * to a Twig variable call.
     * @param array $matches
     * @param int $from
     * @return string
     */
    public static function convertVariableCall(array $matches, &$from)
    {
        // The first possible match is the negation
        $isNegated = $matches[$from++] == "!";
        // The second match is the variable
        $matches[$from]= trim($matches[$from]);
        $result= self::convertVariable($matches[$from++]);
        // The next match is the possible array indexes or object attribute or method references
        $indexes = $matches[$from];
        if (strlen($indexes) > 0) {
            preg_match("/" . self::getVariableIndexRegex(true, true) . "/", $indexes, $indexMatches);
            $indexFrom = 1;
            $result.= self::convertVariableIndexes($indexMatches, $indexFrom);
        }

        if ($isNegated == true) {
            $result = self::convertNegated($result);
        }

        return $result;
    }

    /**
     * Converts PHP variable indexes to Twig variable indexes.
     * @param array $matches
     * @param int $from
     * @return string
     */
    public static function convertVariableIndexes(array $matches, &$from)
    {
        $result= "";
        $count= count($matches);
        for ($i= $from; $i < $count; $i++) {
            $index= trim($matches[$i], "[] \t\n\r\0\x0B");
            $length= strlen($index);

            if ($length <= 0) {
                continue;
            }

            // String index
            if ($index[0] == '"' || $index[0] == "'") {
                $result .= "." . self::convertString($index);
            // Int index
            } elseif (is_numeric($index)) {
                $result .= "." . self::convertInt($index);
            // Boolean index
            } elseif (is_bool($index)) {
                $result .= "." . self::convertBoolean($index);
            // Variable index
            } elseif (strpos($index, "$") === 0) {
                $result .= "[(" . self::convertVariable($index) . ")]";
            // Method
            } elseif (strpos($index, "->")==0 && $length > 4 && $index[$length-2] == "(" && $index[$length-1] == ")") {
                $result .= "." . self::convertMethod($index);
            // Attribute
            } elseif (strpos($index, "->") == 0 && $length > 2) {
                $result .= "." . self::convertAttribute($index);
            }
        }

        return $result;
    }

    /**
     * @param string $attribute
     * @return string
     */
    public static function convertAttribute($attribute)
    {
        return substr($attribute, 2);
    }

    /**
     * @param string $method
     * @return string
     */
    public static function convertMethod($method)
    {
        return substr($method, 2, -2);
    }

    /**
     * @param string $variable
     * @return string
     */
    public static function convertVariable($variable)
    {
        return substr($variable, 1);
    }

    /**
     * @param boolean $boolean
     * @return boolean
     */
    public static function convertBoolean($boolean)
    {
        return $boolean;
    }

    /**
     * @param int $int
     * @return string
     */
    public static function convertInt($int)
    {
        return $int;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function convertString($string)
    {
        if (strlen($string) > 2) {
            return substr($string, 1, -1);
        }
        return $string;
    }

    public static function convertNegated($expression)
    {
        return $expression . " == false";
    }
}
