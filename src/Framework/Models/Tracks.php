<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 14/07/2018
 * Time: 03:34
 */

namespace Colourspace\Framework\Models;


use Colourspace\Container;

class Tracks extends DefaultModel
{

    /**
     * @param bool $doprofiles
     * @throws \Error
     */

    public function startup( $doprofiles=false )
    {

        parent::startup( $doprofiles );

        if( Container::get('application')->session->isLoggedIn() )
            $this->addProfile("Tracks");

        $this->doProfiles();
    }
}