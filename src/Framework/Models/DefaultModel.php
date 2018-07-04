<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 16:49
 */

namespace Colourspace\Framework\Models;


use Colourspace\Container;
use Colourspace\Framework\Profiles\Session;
use Colourspace\Framework\Model;
use Colourspace\Framework\Interfaces\ProfileInterface;
use Colourspace\Framework\Profiles\User;
use Colourspace\Framework\Profiles\Group;

class DefaultModel extends Model
{

    /**
     * @var array
     */

    protected $profiles;

    /**
     * @throws \Error
     */

    public function startup()
    {

        $profiles = [
            'session' => new Session()
        ];

        if( Container::get('application')->session->isLoggedIn() )
        {

            $profiles['user'] = new User();
            $profiles['group'] = new Group();
        }

        $this->object->profiles = new \stdClass();

        foreach ( $profiles as $name=>$profile )
        {

            if( $profile instanceof ProfileInterface == false )
                throw new \Error('Profile invalid');

            $profile->create();
            $this->object->profiles->$name = $profile->get();
        }

        parent::startup();
    }
}