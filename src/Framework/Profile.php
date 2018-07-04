<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 22:41
 */

namespace Colourspace\Framework;


use Colourspace\Container;
use Colourspace\Framework\Interfaces\ProfileInterface;
use Colourspace\Framework\Util\Collector;
use Colourspace\Framework\Util\Debug;

class Profile implements ProfileInterface
{

    /**
     * This prevents us from creating multiple instances of the same class per executing and instead use ones stored in an array
     *
     * @var array
     */
    protected static $shared_classes = [];
    /**
     * @var \stdClass
     */
    protected $objects;

    /**
     * Profile constructor.
     * @param array $classes
     * @throws \Error
     */

    public function __construct( $classes=[] )
    {

        if( Container::has('application') == false )
                throw new \Error('Container has not been initiated');

            $this->objects = new \stdClass();

        if( empty( $classes ) == false )
            $this->processClasses( $classes );
    }


    /**
     * @return mixed
     */


    public function toArray()
    {

        return( json_decode( json_encode( $this->objects ), true ));
    }

    /**
     * @return \stdClass
     */

    public function get()
    {

        return( $this->objects );
    }

    /**
     * @return null|void
     * @throws \Error
     */

    public function create()
    {

        if ( DEBUG_ENABLED )
            Debug::message("Profile create: " .  __CLASS__);
    }

    /**
     * @param $name
     * @return mixed
     */

    public function __get($name)
    {

        return ( $this->objects->$name );
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */

    public function __set($name, $value)
    {

        return ( $this->objects->$name = $value );
    }

    /**
     * @return bool
     * @throws \Error
     */

    public function isLoggedIn()
    {

        if( Container::get('application')->session->isActive() == false )
            return false;

        if( Container::get('application')->session->isLoggedIn() == false )
            return false;

        return true;
    }

    /**
     * @param $class
     * @return mixed
     */

    public function class($class )
    {

        return( self::$shared_classes[ $class ] );
    }

    /**
     * @param $class
     * @return bool
     */

    public function hasClass( $class )
    {

        return( isset( self::$shared_classes[ $class ] ) );
    }

    /**
     * @param $classes
     * @param bool $collector
     * @throws \Error
     */

    private function processClasses( $classes, $collector=true )
    {

        foreach ( $classes as $class )
        {

            $namespace = $class[0];
            $class = $class[1];

            foreach( self::$shared_classes as $key=>$value )
            {

                if( $key == $namespace && $class == $value )
                    return;
            }

            if( $collector )
            {

                self::$shared_classes[ $class ] = Collector::new( $class, $namespace );
            }
            else
            {

                $real_namespace = $namespace . $class;

                if( class_exists( $real_namespace ) == false )
                    throw new \Error('Invalid class given');

                self::$shared_classes[ $class ] = new $real_namespace;
            }
        }
    }
}