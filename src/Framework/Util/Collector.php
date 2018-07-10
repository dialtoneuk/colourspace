<?php
namespace Colourspace\Framework\Util;


class Collector
{

    /**
     * @var \stdClass
     */

    protected static $classes;

    /**
     * @throws \Error
     */

    public static function initialize()
    {

        if( DEBUG_ENABLED )
            Debug::message("Collector intialized");

        self::$classes = [];
    }

    /**
     * @param null $namespace
     * @param $class
     * @return mixed
     * @throws \Error
     */

    public static function new( $class, $namespace=null )
    {

        if( self::hasInitialized() == false )
            self::initialize();

        if( $namespace == null )
            $namespace = COLLECTOR_DEFAULT_NAMESPACE;

        if( self::exists( $namespace, $class ) == false )
            throw new \Error("Namespace does not exist: " . $namespace . $class );

        if( isset( self::$classes[ $namespace . $class ] ) )
        {

            Debug::message("Collector returning pre created class: " . $class );

            return( self::$classes[ $namespace . $class ]  );
        }


        $full_namespace = $namespace . $class;
        self::$classes[ $full_namespace ] = new $full_namespace;

        Debug::message("Collector returning newly created class: " . $class );

        return( self::$classes[ $full_namespace  ]);
    }

    /**
     * @param $class
     * @param null $namespace
     * @return mixed
     * @throws \Error
     */

    public static function get( $class, $namespace=null )
    {

        if( $namespace == null )
            $namespace = COLLECTOR_DEFAULT_NAMESPACE;

        if( self::hasInitialized() == false )
            throw new \Error("Initialize first");

        return( self::$classes[ $namespace . $class ] );
    }

    /**
     * @param $class
     * @param $as
     * @param null $namespace
     * @return mixed
     * @throws \Error
     */

    public static function as( $class, $as, $namespace=null )
    {

        if( self::hasInitialized() == false )
            self::initialize();

        if( $namespace == null )
            $namespace = COLLECTOR_DEFAULT_NAMESPACE;

        if( isset( self::$classes->$as ) )
            throw new \Error("Class already exists");

        if( self::exists( $namespace, $class ) == false )
            throw new \Error("Namespace does not exist: " . $namespace . $class );

        $full_namespace = $namespace . $class;
        self::$classes[ $as ] = new $full_namespace;

        Debug::message("Collector returning newly created class which is refered to as: " . $as . " ( actual name is " . $class . " )" );

        return( self::$classes[ $as ] );
    }

    /**
     * @param $class
     * @param null $namespace
     * @return bool
     */

    public static function has( $class, $namespace=null )
    {

        return( isset( self::$classes[ $namespace ]) );
    }


    /**
     * @return mixed
     */

    public static function all()
    {

        return( self::$classes );
    }

    /**
     * @param $namespace
     * @param $class
     * @return bool
     */

    private static function exists( $namespace, $class )
    {

        return( class_exists( $namespace . $class ) );
    }

    /**
     * @return bool
     */

    private static function hasInitialized()
    {

        if( empty( self::$classes ) )
            return false;

        return true;
    }
}