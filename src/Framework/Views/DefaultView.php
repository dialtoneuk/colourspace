<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 16:53
 */

namespace Colourspace\Framework\Views;


use Colourspace\Framework\View;

class DefaultView extends View
{


    /**
     * @return array
     * @throws \Error
     */

    public function get()
    {

        $array = parent::get();
        $array["render"] = "index";

        return( $array );
    }
}