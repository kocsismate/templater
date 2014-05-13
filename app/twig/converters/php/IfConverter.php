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
class IfConverter extends Converter
{
    /**
     * @return string
     */
    public function getName()
    {
        return "IF CONVERTER";
    }

    /**
     * @param $template
     * @return string
     */
    public function convert($template)
    {
        /* Lecseréli a <?php if ($warning) ?> típusú template-eket */
        $name= "Variable";
        $template= preg_replace(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::IF_CONDITION_BEGIN . PHPTemplate::CAPTURED_VARIABLE . PHPTemplate::IF_CONDITION_END .
            PHPTemplate::TEMPLATE_END . "/s",
            "{% if \\1 %}",
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

        /* Lecseréli a <?php } ?> típusú template-eket */
        $name= "Closing tag";
        $template= preg_replace(
            "/" . PHPTemplate::TEMPLATE_START .
            "\}" .
            PHPTemplate::TEMPLATE_END . "/s",
            "{% endif %}",
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

        /* Lecseréli a <?php if ($warning == "valami") ?> típusú template-eket. */
        $name= "Array with string index";
        $template= preg_replace(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::IF_CONDITION_BEGIN . PHPTemplate::CAPTURED_VARIABLE . PHPTemplate::EQUALS_OPERATOR .
            PHPTemplate::STRING_LITERAL_START . "([A-Za-z0-9_\-\.]*)" . PHPTemplate::STRING_LITERAL_END .
            PHPTemplate::IF_CONDITION_END . PHPTemplate::TEMPLATE_END . "/s",
            "{% if \\1 == '\\2' %}",
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

        /* Lecseréli a <?php if $warning["valami"] ?> típusú template-eket */
        $name= "Array with string index";
        $template = preg_replace(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::IF_CONDITION_BEGIN . PHPTemplate::CAPTURED_VARIABLE . PHPTemplate::CAPTURED_ARRAY_STRING_INDEX.
            PHPTemplate::IF_CONDITION_END . PHPTemplate::TEMPLATE_END . "/s",
            "{% if \\1.\\2 %}",
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

        /* Lecseréli a <?php if ($warning["valami"] == $valami) ?> típusú template-eket*/
        $name= "Array with string index compares variable";
        $template= preg_replace(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::IF_CONDITION_BEGIN . PHPTemplate::CAPTURED_VARIABLE . PHPTemplate::CAPTURED_ARRAY_STRING_INDEX.
            PHPTemplate::CAPTURED_OPERATOR .
            PHPTemplate::CAPTURED_VARIABLE . PHPTemplate::IF_CONDITION_END . PHPTemplate::TEMPLATE_END . "/s",
            "{% if \\1.\\2 \\3 \\4 %}",
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

        /* Lecseréli a <?php if count($warning)>0 ?> típusú template-eket */
        $name= "Count compares int";
        $template = preg_replace(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::IF_CONDITION_BEGIN . "count" . PHPTemplate::FUNCTION_OPENING_BRACKET .
            PHPTemplate::CAPTURED_VARIABLE . PHPTemplate::FUNCTION_CLOSING_BRACKET .
            PHPTemplate::CAPTURED_OPERATOR . "(" . PHPTemplate::INT_LITERAL . ")" . PHPTemplate::IF_CONDITION_END .
            PHPTemplate::TEMPLATE_END . "/s",
            "{% if \\1|length \\2 \\3  %}",
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
