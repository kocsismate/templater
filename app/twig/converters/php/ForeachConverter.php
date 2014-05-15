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
class ForeachConverter extends Converter
{
    /**
     * @return string
     */
    public function getName()
    {
        return "FOREACH CONVERTER";
    }

    /**
     * @param $template
     * @return string
     */
    public function convert($template)
    {
        /* Lecseréli a <?php foreach($a as $v) ?> típusú template-eket */
        $name= "Value";
        $template = preg_replace(
            "/".PHPTemplate::TEMPLATE_START . PHPTemplate::FOREACH_HEAD_BEGIN .
            PHPConverter::getExpressionRegex() . PHPTemplate::FOREACH_HEAD_CAPTURED_VALUE.
            PHPTemplate::FOREACH_HEAD_END . PHPTemplate::TEMPLATE_END."/s",
            "{% for \\2 in \\1 %}",
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

        /* Lecseréli a <?php foreach($a as $k => $v) ?> típusú template-eket */
        $name= "Key and value";
        $template = preg_replace(
            "/".PHPTemplate::TEMPLATE_START . PHPTemplate::FOREACH_HEAD_BEGIN .
            PHPConverter::getExpressionRegex() . PHPTemplate::FOREACH_HEAD_CAPTURED_KEY_AND_VALUE.
            PHPTemplate::FOREACH_HEAD_END . PHPTemplate::TEMPLATE_END."/s",
            "{% for \\3, \\2 in \\1 %}",
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
