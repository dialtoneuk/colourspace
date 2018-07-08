<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 04/07/2018
 * Time: 00:53
 */

namespace Colourspace\Framework\Views;


use Colourspace\Framework\View;
use Colourspace\Framework\Returns\Page;

class Register extends View
{

    /**
     * @return Page
     * @throws \Error
     */

    public function get()
    {
        
        $array = parent::get();
        $array->add(["file" => "register"]);

        return( $array );
    }
}