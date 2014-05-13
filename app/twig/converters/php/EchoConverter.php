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
        /* Lecseréli a <?php echo $warning; ?> típusú template-eket */
        $name= "Variable";
        $template = preg_replace(
            "/".PHPTemplate::TEMPLATE_START .
            PHPTemplate::ECHO_BEGIN . PHPTemplate::CAPTURED_VARIABLE . PHPTemplate::OPTIONAL_STATEMENT_END .
            PHPTemplate::TEMPLATE_END."/s",
            "{{ \\1 }}",
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

        /* Lecseréli a <?php echo strtolower($warning); ?> típusú template-eket */
        $name= "Strtolower variable ";
        $template = preg_replace(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::ECHO_BEGIN . "strtolower\(\s*" . PHPTemplate::CAPTURED_VARIABLE . "\s*\)" .
            PHPTemplate::OPTIONAL_STATEMENT_END .  PHPTemplate::TEMPLATE_END . "/s",
            "{{ \\1|lower }}",
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

        /* Lecseréli a <?php echo $warning["valami"]; ?> típusú template-eket */
        $name= "Array with string index";
        $template = preg_replace(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::ECHO_BEGIN . PHPTemplate::CAPTURED_VARIABLE . PHPTemplate::CAPTURED_ARRAY_STRING_INDEX .
            PHPTemplate::OPTIONAL_STATEMENT_END . PHPTemplate::TEMPLATE_END . "/s",
            "{{ \\1.\\2 }}",
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

        /* Lecseréli a <?php echo $warning->valami; ?> típusú template-eket */
        $name= "Object";
        $template = preg_replace(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::ECHO_BEGIN . PHPTemplate::CAPTURED_VARIABLE . PHPTemplate::OBJECT_REFERENCE .
            PHPTemplate::CAPTURED_IDENTIFIER .
            PHPTemplate::OPTIONAL_STATEMENT_END . PHPTemplate::TEMPLATE_END . "/s",
            "{{ \\1.\\2 }}",
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

        /* Lecseréli a <?php echo $warning[$variable]; ?> típusú template-eket */
        $name= "Array with variable index";
        $template = preg_replace(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::ECHO_BEGIN . PHPTemplate::CAPTURED_VARIABLE . PHPTemplate::CAPTURED_ARRAY_VARIABLE_INDEX .
            PHPTemplate::OPTIONAL_STATEMENT_END . PHPTemplate::TEMPLATE_END . "/s",
            "{{ \\1.\\2 }}",
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

        /* Lecseréli a <?php echo $warning[0]; ?> típusú template-eket */
        $name= "Array with int index";
        $template = preg_replace(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::ECHO_BEGIN . PHPTemplate::CAPTURED_VARIABLE . PHPTemplate::CAPTURED_ARRAY_INT_INDEX .
            PHPTemplate::OPTIONAL_STATEMENT_END . PHPTemplate::TEMPLATE_END . "/s",
            "{{ \\1.\\2 }}",
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

        /* Lecseréli a <?php echo count($warning["valami"]); ?> típusú template-eket */
        $name= "Count array with string index";
        $template = preg_replace(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::ECHO_BEGIN . "count" . PHPTemplate::FUNCTION_OPENING_BRACKET .
            PHPTemplate::CAPTURED_VARIABLE . PHPTemplate::CAPTURED_ARRAY_STRING_INDEX .
            PHPTemplate::FUNCTION_CLOSING_BRACKET.PHPTemplate::OPTIONAL_STATEMENT_END .PHPTemplate::TEMPLATE_END . "/s",
            "{{ \\1|length }}",
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
