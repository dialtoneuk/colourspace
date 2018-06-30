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

    public function formMessage( $name, $value );

    public function formError( $name, $value );

    public function toArray();
}