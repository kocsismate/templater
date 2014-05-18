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
        $name = "Conditional Statements";
        $template = preg_replace_callback(
            "/" . PHPTemplate::TEMPLATE_START . PHPTemplate::IF_CONDITION_BEGIN .
            PHPConverter::getConditionalExpressionRegex(true) .
            PHPTemplate::IF_CONDITION_END . PHPTemplate::TEMPLATE_END . "/s",
            function ($matches) {
                $from = 1;
                return "{% if " . PHPConverter::convertConditionalExpression($matches, $from) . " %}";
            },
            $template,
            -1,
            $count
        );
        if (isset($this->conversionInfo[$name])) {
            $this->conversionInfo[$name] += $count;
        } else {
            $this->conversionInfo[$name] = 0;
        }
        if ($count !== 0) {
            return $template;
        }

        /* Lecseréli a <?php elseif ($warning) ?> típusú template-eket */
        $name = "Elseif Statements";
        $template = preg_replace_callback(
            "/" . PHPTemplate::TEMPLATE_START . PHPTemplate::ELSE_IF_CONDITION_BEGIN .
            PHPConverter::getConditionalExpressionRegex(true) .
            PHPTemplate::IF_CONDITION_END . PHPTemplate::TEMPLATE_END . "/s",
            function ($matches) {
                $from = 1;
                return "{% elseif " . PHPConverter::convertConditionalExpression($matches, $from) . " %}";
            },
            $template,
            -1,
            $count
        );
        if (isset($this->conversionInfo[$name])) {
            $this->conversionInfo[$name] += $count;
        } else {
            $this->conversionInfo[$name] = 0;
        }
        if ($count !== 0) {
            return $template;
        }

        /* Lecseréli a <?php else ?> típusú template-eket */
        $name = "Else Statements";
        $template = preg_replace(
            "/" . PHPTemplate::TEMPLATE_START . PHPTemplate::ELSE_BEGIN . PHPTemplate::TEMPLATE_END . "/s",
            "{% else %}",
            $template,
            -1,
            $count
        );
        if (isset($this->conversionInfo[$name])) {
            $this->conversionInfo[$name] += $count;
        } else {
            $this->conversionInfo[$name] = 0;
        }
        if ($count !== 0) {
            return $template;
        }

        /* Lecseréli a <?php endif; ?> típusú template-eket */
        $name = "Endif Statements";
        $template = preg_replace(
            "/" . PHPTemplate::TEMPLATE_START . PHPTemplate::ENDIF_STATEMENT . PHPTemplate::TEMPLATE_END . "/s",
            "{% endif %}",
            $template,
            -1,
            $count
        );
        if (isset($this->conversionInfo[$name])) {
            $this->conversionInfo[$name] += $count;
        } else {
            $this->conversionInfo[$name] = 0;
        }
        if ($count !== 0) {
            return $template;
        }

        return $template;
    }
}
