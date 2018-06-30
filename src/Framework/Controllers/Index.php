<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 15:19
 */

namespace Colourspace\Framework\Controllers;


use Colourspace\Framework\Controller;

class Index extends Controller
{

    /**
     * @param string $type
     * @param $data
     */

    public function process(string $type, $data)
    {


    }

    /**
     * @param string $type
     * @param $data
     * @return bool
     */

    public function authentication(string $type, $data)
    {

       return true;
    }
}