<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 15:40
 */

namespace Colourspace\Framework;


use Colourspace\Framework\Util\FileOperator;

class Router
{

    /**
     * The actual; file
     * @var FileOperator
     */

    protected $routes;

    /**
     * The live routes which are fed to Flight
     * @var array
     */

    public $live_routes;

    /**
     * Router constructor.
     * @param bool $auto_initialize
     * @throws \Error
     */

    public function __construct( $auto_initialize=true )
    {

        $this->routes = new FileOperator( COLOURSPACE_ROOT . ROUTER_ROUTES, true );

        if( $this->routes->isEmpty() )
            throw new \Error('Route file is empty');

        if( $auto_initialize )
            $this->initialize();
    }

    /**
     * @throws \Error
     */

    public function initialize()
    {

        $routes = $this->routes->decodeJSON( true );

        if( empty( $routes ) )
            throw new \Error('Routes are empty, check file');

        foreach ( $routes as $key=>$route )
        {

            if( $this->check( $route ) == false )
                throw new \Error('Route failed check at index: ' . $key );

            if( isset( $this->live_routes[ $route['url'] ] ) )
                continue;

            $this->live_routes[ $route['url'] ] = $this->lower( $route['mvc'] );
        }
    }

    /**
     * @return bool
     */

    public function test()
    {

        if( empty( $this->live_routes ) )
            return false;

        foreach( $this->live_routes as $route )
        {

            if( empty( $route ) )
                return false;
        }

        return true;
    }

    /**
     * @param $url
     * @return bool
     */

    public function hasRoute( $url )
    {

        return( isset( $this->live_routes[ $url ] ) );
    }

    /**
     * @param $url
     * @return mixed
     */

    public function getRoute( $url )
    {

        return( $this->live_routes[ $url ] );
    }

    /**
     * @param $url
     * @return mixed
     */

    public function getRoutePayload( $url )
    {

        returN( $this->live_routes[ $url ]['mvc'] );
    }

    /**
     * Changes the keys to be lower case
     * @param array $mvc
     * @return array
     */

    private function lower( array $mvc )
    {

        foreach ( $mvc as $key=>$value )
            $mvc[ strtolower( $key ) ] = $value ;

        return $mvc;
    }

    /**
     * @param $route
     * @return bool
     */

    private function check( $route )
    {

        if( isset( $route['url'] ) == false )
            return false;

        if( isset( $route['mvc'] ) == false )
            return false;

        if( count( $route['mvc'] ) !== 3 )
            return false;

        foreach( $route['mvc'] as $key=>$type )
        {

            if( strtolower( $key ) !== ( MVC_TYPE_MODEL | MVC_TYPE_VIEW | MVC_TYPE_CONTROLLER ) )
                return false;
        }

        return true;
    }
}