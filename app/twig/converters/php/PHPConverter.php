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
        return array("&&", "||");
    }

    public static function getLogicalUnaryOperatorList()
    {
        return array("!");
    }

    public static function getFunctionNameList()
    {
        return array("isset", "empty", "trim", "strtolower", "count", "is_array");
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
            $pattern .= "\\" . $list[$i];
        }
        $pattern .= ")\s*";

        return $pattern;
    }

    public static function getLogicalUnaryOperatorRegex($isOptional = true, $isCaptured = false)
    {
        $pattern = "\s*(" . ($isCaptured == true ? "" : "?:");

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
        $pattern = "\s*(" . ($isCaptured == true ? "" : "?:");
        $list = self::getFunctionNameList();
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
        return "(" . ($isCaptured == true ? "" : "?:") . "\'[^\.\-\']*\'|\"[^\.\-\"\$]*\")"; // TODO Not containing . -
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
        $pattern = "\\$" . self::getIdentifierRegex(false);
        if ($isCaptured == true) {
            $pattern = "($pattern)";
        }

        return "\s*$pattern\s*";
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
                self::getConditionRegex() .
                "(?:" .
                    self::getLogicalBinaryOperatorRegex(true) .
                    self::getConditionRegex() .
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
                self::getExpressionRegex() .
                "(?:" .
                    self::getComparatorRegex(false) .
                    self::getExpressionRegex() .
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
            "(?:" .
            self::getArgumentSeparatorRegex(false) . "|" .
            self::getVariableCallRegex($isCaptured) . "|" .
            self::getBooleanLiteralRegex($isCaptured) . "|" .
            self::getIntLiteralRegex($isCaptured) . "|" .
            self::getStringLiteralRegex($isCaptured) .
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
            "(" . ($isCaptured == true ? "" : "?:") . "(?:" .
            self::getArrayIntIndexRegex(false) . "|" . // Int array index
            self::getArrayStringIndexRegex(false) . "|" . // String array index
            self::getArrayVariableIndexRegex(false) . "|" . // Variable array index
            self::getObjectReferenceRegex(false) . self::getIdentifierRegex(false) . "\(\)|" . // Method
            self::getObjectReferenceRegex(false) . self::getIdentifierRegex(false) . // Attribute
            ")*)";
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
        // The first match is all the operands of the expression
        $allArguments = $matches[$from];
        $operands = preg_split("/[\?\:]/", $allArguments); // TODO: not good for strings containing special chars

        foreach ($operands as &$a) {
            $a = trim($a);
        }

        $num= 0;
        $result = self::convertConditionalExpression($operands, $num);
        $result.= " ? ";
        $num++;
        $result.= self::convertExpression($matches, $num);
        $result.= " : ";
        $num++;
        $result.= self::convertExpression($matches, $num);

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
        $allArguments = $matches[$from];
        $operands = preg_split("/(?:&&|\|\|)/", $allArguments); // TODO: not good for strings containing special chars
        preg_match_all("/(&&|\|\|)/", $allArguments, $operators);
        $operators = $operators[1];

        foreach ($operands as &$a) {
            $a = trim($a);
        }

        $result = "";
        $count = count($operands);
        for ($i = 0; $i < $count; $i++) {
            preg_match("/" . self::getConditionRegex(true) . "/", $operands[$i], $operandMatches);
            $operandFrom = 1;
            $result .= self::convertCondition($operandMatches, $operandFrom);

            if ($i + 1 < $count) {
                $result .= (" " . self::convertLogicalOperator($operators[$i]) . " ");
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
        $operators = array("&&" => "AND", "||" => "OR", "!" => "NOT");
        return in_array($operator, $operators) ? $operators[$operator] : $operator;
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
        $allArguments = $matches[$from];
        $operands = preg_split("/(?:<|<=|!=|==|>=|>)/", $allArguments); // TODO: not good for strings containing special chars
        preg_match_all("/(<|<=|!=|==|>=|>)/", $allArguments, $operators);
        $operators = $operators[1];

        foreach ($operands as &$a) {
            $a = trim($a);
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
        $allArguments = $matches[$from];
        $operands = preg_split("/[\+\-\*\/\%\.]/", $allArguments); // TODO: not good for strings containing special chars
        preg_match_all("/([\+\-\*\/\%\.])/", $allArguments, $operators);
        $operators = $operators[1];

        foreach ($operands as &$a) {
            $a = trim($a);
        }

        $result = "";
        $count = count($operands);
        for ($i = 0; $i < $count; $i++) {
            preg_match("/" . self::getPrimitiveCallRegex(true) . "/", $operands[$i], $operandMatches);
            $operandFrom = 1;
            $result .= self::convertPrimitiveCall($operandMatches, $operandFrom);

            if ($i + 1 < $count) {
                $result .= (" " . $operators[$i] . " ");
            }
        }

        return $result;
    }

    /**
     * Converts a PHP primitive expression to Twig format.
     * @param array $matches
     * @param int $from
     * @return string
     */
    public static function convertPrimitiveCall(array $matches, &$from)
    {
        $p = $matches[$from];

        // String literal
        if (strpos($p, '"') === 0 || strpos($p, "'") === 0) {
            return self::convertString($p);
        }

        // Int literal
        if (is_numeric($p)) {
            return self::convertInt($p);
        }

        // Boolean literal
        if (is_bool($p)) {
            return self::convertBoolean($p);
        }

        // Function call
        foreach (self::getFunctionNameList() as $f) {
            $pos = strpos($p, $f);
            if ($pos === 0 || ($pos === 1 && strpos($p, "!") === 0)) {
                preg_match("/" . self::getFunctionCallRegex(true) . "/", $p, $functionMatches);
                $functionFrom = 1;
                return self::convertFunctionCall($functionMatches, $functionFrom);
            }
        }

        // Variable call
        $pos = strpos($p, "$");
        if ($pos === 0 || ($pos === 1 && strpos($p, "!") === 0)) {
            preg_match("/" . self::getVariableCallRegex(true) . "/", $p, $variableMatches);
            $variableFrom = 1;
            return self::convertVariableCall($variableMatches, $variableFrom);
        }

        return $p;
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
        $argumentMatches = preg_split("/,\n/", $allArguments); // TODO: not good for strings containing ,
        foreach ($argumentMatches as &$a) {
            $a = trim($a);
        }

        $arguments = array();
        $count = count($argumentMatches);
        for ($i = 0; $i < $count; $i++) {
            $p = $argumentMatches[$i];
            // String index
            if (strpos($p, '"') === 0 || strpos($p, "'") === 0) {
                $arguments[] = self::convertString($p);
                // Int index
            } elseif (is_numeric($p)) {
                $arguments[] = self::convertInt($p);
                // Boolean index
            } elseif (is_bool($p)) {
                $arguments[] = self::convertBoolean($p);
                // Variable index
            } elseif (strpos($p, "$") === 0 || strpos($p, "!") === 0) {
                preg_match("/" . self::getVariableCallRegex(true) . "/", $p, $variableMatches);
                $variableFrom = 1;
                $arguments[] = self::convertVariableCall($variableMatches, $variableFrom);
            }
        }

        $result = "";
        switch ($function) {
            case "date":
                $result = (isset($arguments[1])? $arguments[1] : "\"now\"") . "|date($arguments[0])";
                break;
            case "isset":
                $result = $arguments[0] . " is defined";
                break;
            case "empty":
                $result = $arguments[0] . " is empty";
                break;
            case "trim":
                $result = $arguments[0] . "|trim";
                if (isset($arguments[1])) {
                    $result .= "('" . $arguments[1] . "')";
                }
                break;
            case "strtolower":
                $result = $arguments[0] . "|lower";
                break;
            case "count":
                $result = $arguments[0] . "|length";
                break;
            case "is_array":
                $result = $arguments[0] . " is iterable";
                break;
        }

        if ($isNegated == true) {
            $result = self::convertNegated($result);
        }

        return $result;
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
        $result = self::convertVariable($matches[$from++]);
        // The next match is the possible array indexes or object attribute or method references
        $indexes = $matches[$from];
        if (strlen($indexes) > 0) {
            $indexParts = preg_split("/(?:\[|\]|->)/", $indexes); //FIXME not good for strings containing special chars!
            // Then we start iterating from the first alternative
            foreach ($indexParts as $p) {
                if (strlen($p) <= 0) {
                    continue;
                }
                $p = trim($p);
                // String index
                if ($p[0] == '"' || $p[0] == "'") {
                    $result .= "." . self::convertString($p);
                    // Int index
                } elseif (is_numeric($p)) {
                    $result .= "." . self::convertInt($p);
                    // Boolean index
                } elseif (is_bool($p)) {
                    $result .= "." . self::convertBoolean($p);
                    // Variable index
                } elseif (strpos($p, "$") === 0) {
                    $result .= "[(" . self::convertVariable($p) . ")]";
                    // Method
                } elseif (strpos($p, "->") == 0 && strlen($p) > 4 && $p[strlen($p) - 2] == "(" && $p[strlen($p) - 1] == ")") {
                    $result .= "." . self::convertMethod($p);
                    // Attribute
                } elseif (strpos($p, "->") == 0 && strlen($p) > 2) {
                    $result .= ".$p";
                }
            }
        }

        if ($isNegated == true) {
            $result = self::convertNegated($result);
        }

        return $result;
    }

    /**
     * @param string $attribute
     * @return string
     */
    private static function convertAttribute($attribute)
    {
        return substr($attribute, 2);
    }

    /**
     * @param string $method
     * @return string
     */
    private static function convertMethod($method)
    {
        return substr($method, 0, -2);
    }

    /**
     * @param string $variable
     * @return string
     */
    private static function convertVariable($variable)
    {
        return substr($variable, 1);
    }

    /**
     * @param boolean $boolean
     * @return boolean
     */
    private static function convertBoolean($boolean)
    {
        return $boolean;
    }

    /**
     * @param int $int
     * @return string
     */
    private static function convertInt($int)
    {
        return $int;
    }

    /**
     * @param string $string
     * @return string
     */
    private static function convertString($string)
    {
        if (strlen($string) > 2) {
            return substr($string, 1, -1);
        }
        return $string;
    }

    private static function convertNegated($expression)
    {
        return $expression . " is false"; // TODO When is already present, say "is not"
    }
}
