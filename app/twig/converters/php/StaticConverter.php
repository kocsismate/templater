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
class StaticConverter extends Converter
{
    /**
     * @return string
     */
    public function getName()
    {
        return "STATIC CONVERTER";
    }

    /**
     * @param string $tag
     * @return string
     */
    public function convert($tag)
    {
        $name = "Echoing static method call without parameters";
        $count= 0;
        $tag = preg_replace(
            "/" . PHPTemplate::TEMPLATE_START .
            PHPTemplate::ECHO_BEGIN .
            PHPConverter::getIdentifierRegex(true) . "\:\:" . PHPConverter::getIdentifierRegex(true) . "\s*\(\s*\)\s*" .
            PHPTemplate::ECHO_OPTIONAL_END . PHPTemplate::OPTIONAL_STATEMENT_END . PHPTemplate::TEMPLATE_END . "/s",
            "{{ staticCall(\"\\1\", \"\\2\") }}",
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
