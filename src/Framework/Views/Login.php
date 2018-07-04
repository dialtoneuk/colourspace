<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 04/07/2018
 * Time: 00:53
 */

namespace Colourspace\Framework\Views;


use Colourspace\Framework\View;

class Login extends View
{

    /**
     * @return array
     * @throws \Error
     */

    public function get()
    {

        $array = parent::get();
        $array["render"] = "login";

        return( $array );
    }
}