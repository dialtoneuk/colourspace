<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 15:19
 */

namespace Colourspace\Framework\Controllers;


use Colourspace\Framework\Controller;
use Colourspace\Framework\Util\Debug;

class DefaultController extends Controller
{

    /**
     * @param string $type
     * @param $data
     * @throws \Error
     */

    public function process(string $type, $data)
    {

        if( DEBUG_ENABLED )
            Debug::message("Default contrller process called");
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