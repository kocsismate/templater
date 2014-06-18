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
class SetConverter extends Converter
{
    /**
     * @return string
     */
    public function getName()
    {
        return "SET CONVERTER";
    }

    /**
     * @param string $tag
     * @return string
     */
    public function convert($tag)
    {
        $name = "Expression";
        $tag = preg_replace_callback(
            "/" .
            PHPTemplate::TEMPLATE_START .
            PHPConverter::getVariableRegex(true) . '\s*=\s*' .
            PHPConverter::getExpressionRegex(true) .
            PHPTemplate::OPTIONAL_STATEMENT_END .
            PHPTemplate::TEMPLATE_END .
            "/s",
            function ($matches) {
                $variable= PHPConverter::convertVariable($matches[1]);
                $from = 2;
                return "{% set " . $variable . " = " . PHPConverter::convertExpression($matches, $from) . " %}";
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

        $name = "Postfix increase";
        $tag = preg_replace_callback(
            "/" .
            PHPTemplate::TEMPLATE_START .
            PHPConverter::getVariableRegex(true) . '\s*\+\+\s*' .
            PHPTemplate::OPTIONAL_STATEMENT_END .
            PHPTemplate::TEMPLATE_END .
            "/s",
            function ($matches) {
                $variable= PHPConverter::convertVariable($matches[1]);
                return "{% set $variable = $variable + 1 %}";
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

        $name = "Prefix increase";
        $tag = preg_replace_callback(
            "/" .
            PHPTemplate::TEMPLATE_START .
            '\s*\+\+\s*' .
            PHPConverter::getVariableRegex(true) .
            PHPTemplate::OPTIONAL_STATEMENT_END .
            PHPTemplate::TEMPLATE_END .
            "/s",
            function ($matches) {
                $variable= PHPConverter::convertVariable($matches[1]);
                return "{% set $variable = $variable + 1 %}";
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

        $name = "Postfix decrease";
        $tag = preg_replace_callback(
            "/" .
            PHPTemplate::TEMPLATE_START .
            PHPConverter::getVariableRegex(true) . '\s*\-\-\s*' .
            PHPTemplate::OPTIONAL_STATEMENT_END .
            PHPTemplate::TEMPLATE_END .
            "/s",
            function ($matches) {
                $variable= PHPConverter::convertVariable($matches[1]);
                return "{% set $variable = $variable - 1 %}";
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

        $name = "Prefix decrease";
        $tag = preg_replace_callback(
            "/" .
            PHPTemplate::TEMPLATE_START .
            '\s*\-\-\s*' .
            PHPConverter::getVariableRegex(true) .
            PHPTemplate::OPTIONAL_STATEMENT_END .
            PHPTemplate::TEMPLATE_END .
            "/s",
            function ($matches) {
                $variable= PHPConverter::convertVariable($matches[1]);
                return "{% set $variable = $variable - 1 %}";
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
