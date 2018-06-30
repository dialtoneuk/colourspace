<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 23:10
 */

namespace Colourspace\Framework\Interfaces;


interface ProfileInterface
{

    /**
     * @return object
     */

    public function get();

    /**
     * @return array
     */

    public function toArray();

    /**
     * @return null
     */

    public function create();
}