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
use Colourspace\Framework\Util\Collector;

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

        parent::startup();


        $profiles = [
            'session' => Collector::new("Session", "Colourspace\\Framework\\Profiles\\")
        ];

        if( Container::get('application')->session->isLoggedIn() )
        {

            $profiles['user'] = Collector::new("User", "Colourspace\\Framework\\Profiles\\");
            $profiles['group'] = Collector::new("Group", "Colourspace\\Framework\\Profiles\\");
            $profiles['tracks'] = Collector::new("Tracks", "Colourspace\\Framework\\Profiles\\");
        }

        $this->object->profiles = new \stdClass();

        foreach ( $profiles as $name=>$profile )
        {

            if( $profile instanceof ProfileInterface == false )
            {

                die( print_r( $profile ) );
            }


            $profile->create();
            $this->object->profiles->$name = $profile->get();
        }
    }
}