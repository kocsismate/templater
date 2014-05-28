<?php
namespace app\twig\converters\php;

use app\Converter;
use app\php\PHPTemplate;

/**
 * Az osztály rövid leírása
 *
 * Az osztály hosszú leírása, példakód
 * akár több sorban is
 *
 * @package
 * @author kocsismate
 * @since 2014.05.13. 13:28
 */
class EchoConverter extends Converter
{
    /**
     * @return string
     */
    public function getName()
    {
        return "ECHO CONVERTER";
    }

    /**
     * @param string $tag
     * @return string
     */
    public function convert($tag)
    {
        $name = "Expression";
        $tag = preg_replace_callback(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::ECHO_BEGIN .
            PHPConverter::getExpressionRegex(true) .
            PHPTemplate::ECHO_OPTIONAL_END . PHPTemplate::OPTIONAL_STATEMENT_END . PHPTemplate::TEMPLATE_END . "/s",
            function ($matches) {
                $from = 1;
                return "{{ " . PHPConverter::convertExpression($matches, $from) . " }}";
            },
            $tag,
            -1,
            $count
        );
        if (isset($this->conversionInfo[$name])) {
            $this->conversionInfo[$name] += $count;
        } else {
            $this->conversionInfo[$name] = 0;
        }
        if ($count !== 0) {
            return $tag;
        }

        $name = "Ternary operator call";
        $tag = preg_replace_callback(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::ECHO_BEGIN .
            PHPConverter::getTernaryOperatorExpressionRegex(true) .
            PHPTemplate::ECHO_OPTIONAL_END . PHPTemplate::OPTIONAL_STATEMENT_END . PHPTemplate::TEMPLATE_END . "/s",
            function ($matches) {
                $from = 1;
                return "{{ " . PHPConverter::convertTernaryOperatorExpression($matches, $from) . " }}";
            },
            $tag,
            -1,
            $count
        );
        if (isset($this->conversionInfo[$name])) {
            $this->conversionInfo[$name] += $count;
        } else {
            $this->conversionInfo[$name] = 0;
        }
        if ($count !== 0) {
            return $tag;
        }

        $name = "Printf";
        $tag = preg_replace_callback(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPConverter::getFunctionCallRegex(true) .
            PHPTemplate::OPTIONAL_STATEMENT_END . PHPTemplate::TEMPLATE_END . "/s",
            function ($matches) {
                $from = 1;
                return "{{ " . PHPConverter::convertFunctionCall($matches, $from) . " }}";
            },
            $tag,
            -1,
            $count
        );
        if (isset($this->conversionInfo[$name])) {
            $this->conversionInfo[$name] += $count;
        } else {
            $this->conversionInfo[$name] = 0;
        }
        if ($count !== 0) {
            return $tag;
        }

        return $tag;
    }
}
