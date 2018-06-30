<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 16:49
 */

namespace Colourspace\Framework\Models;


use Colourspace\Container;
use Colourspace\Framework\Model;
use Colourspace\Framework\Profiles\User;

class Common extends Model
{

    /**
     * @var User
     */

    protected $profile;

    /**
     * @throws \Error
     */

    public function startup()
    {

        parent::startup();

        if( Container::get('application')->session->isLoggedIn() == false )
            return;

        $this->profile = new User();
        $this->profile->create();

        //If we were doing multiple profiles we would instead do an array
        $this->object->profile = $this->profile->get();
    }
}