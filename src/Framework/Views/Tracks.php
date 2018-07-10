<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 16:53
 */

namespace Colourspace\Framework\Views;


use Colourspace\Framework\View;
use Colourspace\Framework\Returns\Page;

class Tracks extends View
{


    /**
     * @return Page
     * @throws \Error
     */

    public function get()
    {

        $array = parent::get();
        $array->add(["file" => "tracks"]);

        return( $array );
    }
}