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
     * @param $template
     * @return string
     */
    public function convert($template)
    {
        $name= "Expression";
        $template = preg_replace_callback(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::ECHO_BEGIN .
            PHPConverter::getExpressionRegex() .
            PHPTemplate::ECHO_OPTIONAL_END . PHPTemplate::OPTIONAL_STATEMENT_END . PHPTemplate::TEMPLATE_END . "/s",
            function ($matches) {
                return "{{ " . PHPConverter::convertVariableCall($matches, 1) . " }}";
            },
            $template,
            -1,
            $count
        );
        if (isset($this->conversionInfo[$name])) {
            $this->conversionInfo[$name]+= $count;
        } else {
            $this->conversionInfo[$name]= 0;
        }
        if ($count !== 0) {
            return $template;
        }

        $name= "Ternary operator call";
        $template = preg_replace_callback(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::ECHO_BEGIN .
            PHPConverter::getTernaryOperatorExpressionRegex() .
            PHPTemplate::ECHO_OPTIONAL_END .PHPTemplate::OPTIONAL_STATEMENT_END . PHPTemplate::TEMPLATE_END . "/s",
            function ($matches) {
                return "{{ " . PHPConverter::convertVariableCall($matches, 1) . " }}";
            },
            $template,
            -1,
            $count
        );
        if (isset($this->conversionInfo[$name])) {
            $this->conversionInfo[$name]+= $count;
        } else {
            $this->conversionInfo[$name]= 0;
        }
        if ($count !== 0) {
            return $template;
        }

        $name= "Printf";
        $template = preg_replace_callback(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::PRINTF_BEGIN .
            PHPConverter::getExpressionRegex() . PHPTemplate::CAPTURED_OPTIONAL_ARGUMENT_SEPARATOR .
            PHPConverter::getExpressionRegex() .
            PHPTemplate::PRINTF_END . PHPTemplate::OPTIONAL_STATEMENT_END . PHPTemplate::TEMPLATE_END . "/s",
            function ($matches) {
                return "{{ " . PHPConverter::convertVariableCall($matches, 1) . " }}";
            },
            $template,
            -1,
            $count
        );
        if (isset($this->conversionInfo[$name])) {
            $this->conversionInfo[$name]+= $count;
        } else {
            $this->conversionInfo[$name]= 0;
        }
        if ($count !== 0) {
            return $template;
        }

        return $template;
    }
}
