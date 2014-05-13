<?php
namespace app;

/**
 * Az osztály rövid leírása
 *
 * Az osztály hosszú leírása, példakód
 * akár több sorban is
 *
 * @package
 * @author kocsismate
 * @since 2014.05.12. 10:14
 */
abstract class Converter
{
    /**
     * @var array
     */
    protected $conversionInfo;

    public function __construct()
    {
        $this->conversionInfo= array();
    }

    /**
     * @return string
     */
    abstract public function getName();

    /**
     * @param $template
     * @return string
     */
    abstract public function convert($template);

    /**
     * @return array Keys as names, values as the count of the converted items
     */
    final public function getConversionInfo()
    {
        return $this->conversionInfo;
    }

    /**
     * @return mixed
     */
    final public function echoConversionInfo()
    {
        foreach ($this->conversionInfo as $k => $v) {
            echo "$k: $v<br/>";
        }
        echo "<br/>";
    }

    final public function getConversionInfoSum()
    {
        $sum= 0;
        foreach ($this->conversionInfo as $v) {
            $sum+= $v;
        }

        return $sum;
    }
}
