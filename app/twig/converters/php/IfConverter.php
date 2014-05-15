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
        $name= "Conditional Expression";
        $template= preg_replace_callback(
            "/" . PHPTemplate::TEMPLATE_START . PHPTemplate::IF_CONDITION_BEGIN .
            PHPConverter::getConditionalExpressionRegex() .
            PHPTemplate::IF_CONDITION_END . PHPTemplate::TEMPLATE_END . "/s",
            function ($matches) {
                return "!!! $matches[0]";
                //print_r($matches); echo "<br/><br/>";
                //return "{% if " . PHPConverter::convertCondition($matches, 1, count($matches)) . " %}";
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
