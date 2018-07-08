<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 16:53
 */

namespace Colourspace\Framework\Views;


use Colourspace\Framework\Returns\Json;
use Colourspace\Framework\View;

class Blank extends View
{


    /**
     * @return Json|\Colourspace\Framework\Returns\Page
     */

    public function get()
    {

        $array = new Json();
        $array->setArray(["status" => true ] );

        return ( $array );
    }
}