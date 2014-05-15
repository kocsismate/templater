<?php
namespace app\twig\converters\php;

use app\php\PHPTemplate;

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
    /**
     * @return string
     */
    public static function getConditionalExpressionRegex()
    {
        return
            PHPConverter::getConditionRegex() .
            "(?:".
                PHPTemplate::CAPTURED_LOGICAL_BINARY_OPERATOR .
                PHPConverter::getConditionRegex() .
            ")*";
    }

    /**
     * @return string
     */
    public static function getConditionRegex()
    {
        return
            self::getExpressionRegex() .
            "(?:".
                PHPTemplate::CAPTURED_COMPARATOR .
                self::getExpressionRegex() .
            "){0,1}";
    }

    /**
     * Returns the regex for matching an expression with ternary operator.
     * @return string
     */
    public static function getTernaryOperatorExpressionRegex()
    {
        return
            "\({0,1}" . self::getConditionalExpressionRegex() . "\){0,1}" .
            "\s*\?\s*" . self::getExpressionRegex() . "\s*\:\s*" . self::getExpressionRegex();
    }

    /**
     * Returns a regex for matching a simple expression.
     * @return string
     */
    public static function getExpressionRegex()
    {
        return
            PHPConverter::getPrimitiveCallRegex() .
            "(?:".
                PHPTemplate::CAPTURED_OPERATOR .
                PHPConverter::getPrimitiveCallRegex() .
            ")*";
    }

    /**
     * Returns a regex for matching a function or a variable call. No recursion allowed.
     * @return string
     */
    public static function getPrimitiveCallRegex()
    {
        return
            "(?:" .
                self::getFunctionCallRegex() . "|" .
                self::getVariableCallRegex() . "|" .
                PHPTemplate::CAPTURED_BOOLEAN_LITERAL . "|" .
                PHPTemplate::CAPTURED_INT_LITERAL . "|" .
                PHPTemplate::CAPTURED_STRING_LITERAL .
            ")";
    }

    /**
     * Returns a regex for matching a function call with zero, one or two arguments.
     * @return string
     */
    private static function getFunctionCallRegex()
    {
        return
            PHPTemplate::CAPTURED_OPTIONAL_UNARY_OPERATOR .
            PHPTemplate::CAPTURED_FUNCTION_NAME .
            PHPTemplate::FUNCTION_OPENING_BRACKET .
            "(?:" .
                self::getVariableCallRegex() . "|" .
                PHPTemplate::CAPTURED_OPTIONAL_ARGUMENT_SEPARATOR .
                PHPTemplate::CAPTURED_BOOLEAN_LITERAL . "|" .
                PHPTemplate::CAPTURED_INT_LITERAL . "|" .
                PHPTemplate::CAPTURED_STRING_LITERAL .
            "){0,2}" .
            PHPTemplate::FUNCTION_CLOSING_BRACKET;
    }

    /**
     * @return string
     */
    public static function getVariableCallRegex()
    {
        return
            PHPTemplate::CAPTURED_OPTIONAL_UNARY_OPERATOR .
            PHPTemplate::CAPTURED_VARIABLE_NAME .                                           // Variable
            "((?:".
                PHPTemplate::ARRAY_INT_INDEX . "|" .                                        // Int array index
                PHPTemplate::ARRAY_STRING_INDEX . "|" .                                     // String array index
                PHPTemplate::ARRAY_VARIABLE_INDEX . "|" .                                   // Variable array index
                PHPTemplate::OBJECT_REFERENCE . PHPTemplate::IDENTIFIER . "\(\)|" .         // Method
                PHPTemplate::OBJECT_REFERENCE . PHPTemplate::IDENTIFIER .                   // Attribute
            ")*)";
    }

    //-----------------------------------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------------------------------

    /**
     * @param array $matches
     * @param int $from
     * @param int $to
     * @return string
     */
    private static function convertConditionalSide(array $matches, $from, $to = null)
    {
        if (isset($matches[$from]) === false) {
            return "";
        }
        $m= $matches[$from];

        if ($m == "") {
            return "";
        }
        // Int or string literal
        if (is_numeric($m) || strpos($m, "\"") === 0 || strpos($m, "'") === 0) {
            return $m;
            // Variable call
        } elseif (strpos($m, "$") == 0) {
            return self::convertVariableCall($matches, $from);
            // Other
        } else {
            return "";
        }
    }

    /**
     * Converts a PHP conditional expression to a Twig conditional expression.
     * @param array $matches
     * @param int $from
     * @param int $to
     * @return string
     */
    private static function convertConditionalExpression(array $matches, $from, $to)
    {
        // Left side
        $result= self::convertConditionalSide($matches, $from + 1, $to);

        // Operator and right side is present
        if ($from + 6 < $to) {
            // Operator
            $result.= $matches[$from + 6];
            // Right side
            $result.= self::convertConditionalSide($matches, $from + 7, $to);
        }

        return $result;
    }

    /**
     * @param array $matches
     * @param int $from
     * @param int $to
     * @return string
     */
    private static function convertConditionPart(array $matches, &$from, $to)
    {
        // A part is determined to be before the next logical binary operator
        $expressionTo= $to;
        for ($i= $from; $i < $to; $i++) {
            if ($matches[$i] == "&&" || $matches[$i] == "||") {
                $expressionTo= $i-1;
                break;
            }
        }
        $from= $expressionTo+1;

        return self::convertConditionalExpression($matches, $from, $expressionTo);
    }

    /**
     * @param string $operator
     * @return string
     */
    private static function convertLogicalOperator($operator)
    {
        $operators= array("&&" => "AND", "||" => "OR", "!" => "NOT");
        return in_array($operator, $operators) ? $operators[$operator] : $operator;
    }

    /**
     * Converts a PHP condition to a Twig condition.
     * @param array $matches
     * @param int $from
     * @param int $to
     * @return string
     */
    public static function convertCondition(array $matches, $from, $to)
    {
        if ($to - $from < 0 || isset($matches[$from]) != true || $matches[$from] == "") {
            return $matches[0];
        }

        $result= self::convertConditionPart($matches, $from, $to);

        // Then we start iterating from the first alternative which follows all the alternatives
        for ($i= $from; $i <= $to; $i++) {
            $result.= self::convertLogicalOperator($from);
            $result.= self::convertConditionPart($matches, $from, $to);
            $i= $from;
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
    public static function convertVariableCall(array $matches, $from)
    {
        // The first possible match is the negation
        $isNegated= $matches[$from] == "!";
        // The second match is the variable name
        $result= $matches[$from+1];
        // The next match is the possible array indexes or object attribute or method references
        $indexes= $matches[$from+2];

        $indexMatches= array();
        if (strlen($indexes) > 0) {
            $indexMatches = preg_match_all("/(?:\[\s*(.*)\s*\])/", $indexes);
            $indexMatches = $indexMatches[1];
        }

        if ($isNegated == true) {
            $result.= self::convertNegated($result);
        }

        // Then we start iterating from the first alternative which follows all the alternatives
        foreach ($indexMatches as $m) {
            // String index
            if ($m[0] == '"' || $m[0] == "'") {
                $result.= self::convertString($m);
            // Int index
            } elseif (is_numeric($m)) {
                $result.= self::convertInt($m);
            // Boolean index
            } elseif (is_bool($m)) {
                $result.= self::convertBoolean($m);
            // Variable index
            } elseif (strpos($m, "$") == 0) {
                $result.= substr($m, 1);
            // Attribute
            } else {
                $result.= ".$m";
            }
        }

        return $result;
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

    private static function convertNegated($expression) {
        return $expression . "IS FALSE";
    }
}
