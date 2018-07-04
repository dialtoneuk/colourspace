<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 14:56
 */
namespace Colourspace\Framework\Interfaces;

interface ModelInterface
{

    public function startup();

    public function formMessage($type, $value );

    public function formError($type, $value );

    public function redirect($url, $delay );

    public function toArray();
}