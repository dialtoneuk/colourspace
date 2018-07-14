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
     * @param bool $doprofiles
     * @throws \Error
     */

    public function startup( $doprofiles=true )
    {

        parent::startup();

        $this->object->profiles = new \stdClass();

        $this->addProfile("Session");

        if( Container::get('application')->session->isLoggedIn() )
        {

            $this->addProfile("User");
            $this->addProfile("Group");
        }

        if( $doprofiles )
            $this->doProfiles();
    }

    /**
     * @param $profile
     * @param string $namespace
     */

    public function addProfile( $profile, $namespace="Colourspace\\Framework\\Profiles\\")
    {

        $this->profiles[ $profile ] = $namespace;
    }

    /**
     * @return null
     * @throws \Error
     */

    public function doProfiles()
    {

        if( empty( $this->profiles ) )
            return null;

        foreach( $this->profiles as $key=>$value )
        {

            $profile = Collector::new( $key, $value );


            if( $profile instanceof ProfileInterface == false )
            {

                die("Profile is invalid");
            }

            $name = strtolower( $key );

            $profile->create();
            $this->object->profiles->$name = $profile->get();
        }
    }
}