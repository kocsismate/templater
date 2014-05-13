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
        $name= "Variable and only value";
        $template = preg_replace(
            "/".PHPTemplate::TEMPLATE_START .
            PHPTemplate::FOREACH_HEAD_BEGIN . PHPTemplate::CAPTURED_VARIABLE . PHPTemplate::FOREACH_HEAD_CAPTURED_VALUE.
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
        $name= "Variable and key and value";
        $template = preg_replace(
            "/".PHPTemplate::TEMPLATE_START .
            PHPTemplate::FOREACH_HEAD_BEGIN . PHPTemplate::CAPTURED_VARIABLE .
            PHPTemplate::FOREACH_HEAD_CAPTURED_KEY_AND_VALUE.
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

        /* Lecseréli a <?php foreach($a["valami"] as $v) ?> típusú template-eket */
        $name= "Array and value";
        $template = preg_replace(
            "/".PHPTemplate::TEMPLATE_START .
            PHPTemplate::FOREACH_HEAD_BEGIN . PHPTemplate::CAPTURED_VARIABLE .PHPTemplate::CAPTURED_ARRAY_STRING_INDEX.
            PHPTemplate::FOREACH_HEAD_CAPTURED_VALUE.
            PHPTemplate::FOREACH_HEAD_END . PHPTemplate::TEMPLATE_END."/s",
            "{% for \\4, \\3 in \\1.\\2 %}",
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

        /* Lecseréli a <?php foreach($a["valami"] as $k => $v) ?> típusú template-eket */
        $name= "Array and key and value";
        $template = preg_replace(
            "/".PHPTemplate::TEMPLATE_START .
            PHPTemplate::FOREACH_HEAD_BEGIN . PHPTemplate::CAPTURED_VARIABLE .PHPTemplate::CAPTURED_ARRAY_STRING_INDEX.
            PHPTemplate::FOREACH_HEAD_CAPTURED_KEY_AND_VALUE.
            PHPTemplate::FOREACH_HEAD_END . PHPTemplate::TEMPLATE_END."/s",
            "{% for \\4, \\3 in \\1.\\2 %}",
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

        /* Lecseréli a <?php foreach($a->valami as $v) ?> típusú template-eket */
        $name= "Object and value";
        $template = preg_replace(
            "/".PHPTemplate::TEMPLATE_START .
            PHPTemplate::FOREACH_HEAD_BEGIN . PHPTemplate::CAPTURED_VARIABLE . PHPTemplate::OBJECT_REFERENCE .
            PHPTemplate::CAPTURED_IDENTIFIER .
            PHPTemplate::FOREACH_HEAD_CAPTURED_VALUE.
            PHPTemplate::FOREACH_HEAD_END . PHPTemplate::TEMPLATE_END."/s",
            "{% for \\4, \\3 in \\1.\\2 %}",
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

        /* Lecseréli a <?php foreach($a->valami as $k => $v) ?> típusú template-eket */
        $name= "Object and key and value";
        $template = preg_replace(
            "/".PHPTemplate::TEMPLATE_START .
            PHPTemplate::FOREACH_HEAD_BEGIN . PHPTemplate::CAPTURED_VARIABLE . PHPTemplate::OBJECT_REFERENCE .
            PHPTemplate::CAPTURED_IDENTIFIER .
            PHPTemplate::FOREACH_HEAD_CAPTURED_KEY_AND_VALUE.
            PHPTemplate::FOREACH_HEAD_END . PHPTemplate::TEMPLATE_END."/s",
            "{% for \\4, \\3 in \\1.\\2 %}",
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
