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
    const PHP_TEMPLATE_START= "<\?php\s+";
    const PHP_TEMPLATE_END= "\s*(\?>|\/\/end)";

    /**
     * PHP template-ekből konvertál más template-eket.
     *
     * @param string $fromDir Path name
     * @param string $toFileName File name
     * @return mixed
     */
    abstract public function convertFromPHP($fromDir, $toFileName);

}
