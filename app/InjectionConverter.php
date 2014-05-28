<?php
namespace app;

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
class InjectionConverter extends Converter
{
    /**
     * @var array
     */
    private $injections;

    private $allConversionSum= 0;

    /**
     * @return string
     */
    public function getName()
    {
        return "INJECTION CONVERTER";
    }

    /**
     * @param string $templateFileContent
     * @return string
     */
    public function convert($templateFileContent)
    {
        $i= 1;
        foreach ($this->injections as $key => $injection) {
            $name = "Injection #$i";
            $count= 0;
            $templateFileContent= preg_replace($key, $injection, $templateFileContent, -1, $count);
            if ($count != 0) {
                $this->conversionInfo[$name] = 1;
                $this->allConversionSum+= $count;
                return $templateFileContent;
            }

            $i++;
        }

        return $templateFileContent;
    }

    /**
     * @return array
     */
    public function getInjections()
    {
        return $this->injections;
    }

    /**
     * @return int
     */
    public function getAllConversionSum()
    {
        return $this->allConversionSum;
    }

    /**
     * @param string $projectName
     * @return boolean
     */
    public function setInjectionsFile($projectName)
    {
        $filename= realpath(__DIR__ . "/../temp/$projectName.php");
        if (file_exists($filename) == false) {
            $this->injections = array();
            return false;
        }

        $this->injections = include($filename);

        return true;
    }
}
