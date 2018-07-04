<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 04/07/2018
 * Time: 14:58
 */

namespace Colourspace\Framework\Controllers;


use Colourspace\Container;

class Logout extends Login
{

    /**
     * @param string $type
     * @param $data
     * @throws \Error
     */

    public function process(string $type, $data)
    {

        $this->session->destroy( true );
    }

    /**
     * @return array
     */

    public function keyRequirements()
    {

        return ([]);
    }

    /**
     * @param string $type
     * @param $data
     * @return bool
     * @throws \Error
     */

    public function authentication(string $type, $data)
    {

        $application = Container::get('application');

        if( $application->session->isLoggedIn() == false )
            return false;

        return true;
    }
}