<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 29/06/2018
 * Time: 23:29
 */

namespace Colourspace;


class Container
{

    /**
     * @var array
     */

    private static $objects = [];

    /**
     * @param $name
     * @param $value
     */

    public static function add( $name, $value )
    {

        self::$objects[ $name ] = $value;
    }

    /**
     * @param $name
     * @return bool
     */

    public static function has( $name )
    {

        return( isset( self::$objects[ $name ] ) );
    }

    /**
     * @param $name
     */

    public static function remove( $name )
    {

        unset( self::$objects[ $name ] );
    }

    /**
     * @param $name
     * @return mixed
     */

    public static function get( $name )
    {

        return( self::$objects[ $name ] );
    }

    /**
     * @return array
     */

    public static function all()
    {

        return( self::$objects );
    }

    /**
     * Clears the container
     */

    public static function clear()
    {

        self::$objects = [];
    }
}