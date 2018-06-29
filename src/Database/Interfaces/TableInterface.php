<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 29/06/2018
 * Time: 23:34
 */

namespace Colourspace\Interfaces;

interface TableInterface
{

    /**
     * @return array
     */
    public function map();

    /**
     * @return string
     */
    public function name();
}