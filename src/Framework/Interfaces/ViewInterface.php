<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 14:57
 */

namespace Colourspace\Framework\Interfaces;

interface ViewInterface
{

    public function setModel( ModelInterface $model );

    /**
     * @return ReturnsInterface
     */

    public function get();
}