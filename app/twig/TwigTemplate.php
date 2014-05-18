<?php
namespace app\twig;

use app\php\PHPTemplate;
use app\Template;
use app\twig\converters\php\EchoConverter;
use app\twig\converters\php\ForeachConverter;
use app\twig\converters\php\IfConverter;
use app\twig\converters\php\PHPConverter;

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
final class TwigTemplate extends Template
{
    /**
     * @see \app\Template::getExtension()
     */
    protected function getExtension()
    {
        return "twig";
    }

    /**
     * @return string
     */
    protected function getTemplateCollectorRegex()
    {
        return "";
    }

    /**
     * @see \app\Template::getTempDirectory()
     */
    protected function getTempDirectory()
    {
        return __DIR__ . "/../../temp";
    }

    /**
     * @see \app\Template::convertFromPHP()
     */
    public function convertFromPHP($fromPath, $toFileName)
    {
        $phpTemplate = new PHPTemplate($fromPath);
        $this->templates = $phpTemplate->getTemplates();
        $this->convertTemplatesFromPHP();
        $this->writeTemplatesToFile($toFileName . "-converted", $this->getConvertedTemplates());
        $this->writeTemplatesToFile($toFileName . "-remaining", $this->getRemainingTemplates());
    }

    protected function convertTemplatesFromPHP()
    {
        //echo PHPConverter::getConditionalExpressionRegex(true); exit();
        $echoConverter = new EchoConverter();
        $ifConverter = new IfConverter();
        $foreachConverter = new ForeachConverter();
        $this->templates = array_slice($this->templates, 25);
        foreach ($this->templates as &$t) {
            $t = $echoConverter->convert($t);
            $t = $ifConverter->convert($t);
            $t = $foreachConverter->convert($t);
        }

        echo $echoConverter->getName() . "<br/>";
        $echoConverter->echoConversionInfo();
        echo "----------------------------------------------<br/>";
        echo $ifConverter->getName() . "<br/>";
        $ifConverter->echoConversionInfo();
        echo "----------------------------------------------<br/>";
        echo $foreachConverter->getName() . "<br/>";
        $foreachConverter->echoConversionInfo();

        echo "----------------------------------------------<br/>";
        $sum = $echoConverter->getConversionInfoSum() +
            $ifConverter->getConversionInfoSum() +
            $foreachConverter->getConversionInfoSum();
        echo "TOTAL: $sum<br/>";
    }
}
