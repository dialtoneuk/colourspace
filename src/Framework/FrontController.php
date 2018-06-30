<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 00:24
 */
namespace Colourspace\Framework;

use Colourspace\Framework\Interfaces\ControllerInterface;
use Colourspace\Framework\Interfaces\ModelInterface;
use Colourspace\Framework\Interfaces\ViewInterface;
use Colourspace\Framework\Util\Constructor;

class FrontController
{

    /**
     * @var Constructor
     */
    protected $models;
    /**
     * @var Constructor
     */
    protected $controllers;
    /**
     * @var Constructor
     */
    protected $views;

    //Namespace root for the MVC
    public $namespace;
    //Filepath to the PHP classes for the MVC
    public $filepath;

    /**
     * FrontController constructor.
     * @param bool $auto_initialize
     * @param null $namespace
     * @param null $filepath
     * @throws \Error
     */

    public function __construct( $auto_initialize = true, $namespace = null, $filepath = null )
    {

        if ( $namespace == null )
            $this->namespace = COLOURSPACE_NAMESPACE;
        else
            $this->namespace = $namespace;

        if ( $filepath == null )
            $this->filepath = COLOURSPACE_MVC_ROOT;
        else
            $this->filepath = $filepath;

        if( $auto_initialize == true )
            $this->initialize();
    }

    /**
     * @param array $request
     * @param array $payload
     * @throws \Error
     */

    public function process( array $request, array $payload )
    {

        if( count( $payload ) !== 3 )
            throw new \Error('To many keys in payload');

        foreach ( $payload as $key=>$item )
        {

            if( $key !== ( MVC_TYPE_MODEL || MVC_TYPE_CONTROLLER || MVC_TYPE_VIEW ) )
                throw new \Error('Unknown key type: ' . $key );

            if( $this->has( $key, $item ) == false )
                throw new \Error('Class not found: ' , $key );
        }

        if( isset( $request['method'] ) == false )
            throw new \Error('Request method invalid');

        if( $request['method'] !== ( MVC_REQUEST_POST || MVC_REQUEST_DELETE || MVC_REQUEST_GET || MVC_REQUEST_PUT) )
            throw new \Error('Request method invalid');

        $model = $this->get(  MVC_TYPE_MODEL, $payload[ MVC_TYPE_MODEL ] );
        $controller = $this->get(  MVC_TYPE_CONTROLLER, $payload[ MVC_TYPE_CONTROLLER ] );
        $view = $this->get(  MVC_TYPE_VIEW, $payload[ MVC_TYPE_VIEW ] );

        $controller->setModel( $model );
        $controller->process( $request['method'], $request );

        $view->setModel( $model );
        $view->get();
    }

    /**
     * @param object $route
     * @return array
     * @throws \Error
     */

    public function buildRequest( object $route )
    {

        $request = \Flight::request();

        if( empty( $request ) )
            throw new \Error('Flight has not been started');

        if( $request->method !== ( MVC_REQUEST_POST || MVC_REQUEST_DELETE || MVC_REQUEST_GET || MVC_REQUEST_PUT) )
            throw new \Error('Invalid method');

        $array = [
            'method'    => $request->method,
            'ip'        => $request->ip,
            'proxy'     => $request->proxy_ip,
            'url'       => $request->url,
            'params'    => $route->params,
            'contents'  => $route->splat
        ];

        return $array;
    }


    /**
     * @param string $type
     * @param string $class_name
     * @return bool
     */

    public function has( string $type, string $class_name )
    {

        $class_name = strtolower( $class_name );

        switch ( $type )
        {

            case MVC_TYPE_MODEL:
                if( $this->models->has( $class_name ) == false )
                    return false;
                break;
            case MVC_TYPE_VIEW:
                if( $this->views->has( $class_name ) == false  )
                    return false;
                break;
            case MVC_TYPE_CONTROLLER:
                if( $this->controllers->has( $class_name ) == false  )
                    return false;
                break;
        }

        return true;
    }

    /**
     * @param string $type
     * @param string $class_name
     * @return ModelInterface|ControllerInterface|ViewInterface
     */

    public function get( string $type, string $class_name )
    {

        $class_name = strtolower( $class_name );

        switch ( $type )
        {

            case MVC_TYPE_MODEL:
                return $this->models->get( $class_name );
                break;
            case MVC_TYPE_VIEW:
                return $this->views->get( $class_name );
                break;
            case MVC_TYPE_CONTROLLER:
                return $this->controllers->get( $class_name );
                break;
        }

        return null;
    }

    /**
     * @param string $type
     * @param string $class_name
     * @return null|void
     */

    public function remove( string $type, string $class_name )
    {

        $class_name = strtolower( $class_name );

        switch ( $type )
        {

            case MVC_TYPE_MODEL:
                $this->models->remove( $class_name );
                break;
            case MVC_TYPE_VIEW:
                $this->views->remove( $class_name );
                break;
            case MVC_TYPE_CONTROLLER:
                $this->controllers->remove( $class_name );
                break;
        }
    }

    /**
     * @param string $type
     * @return Constructor|null
     */

    public function getObject( string $type )
    {
        switch ( $type )
        {

            case MVC_TYPE_MODEL:
                return $this->models;
                break;
            case MVC_TYPE_VIEW:
                return $this->views;
                break;
            case MVC_TYPE_CONTROLLER:
                return $this->controllers;
                break;
        }

        return null;
    }

    /**
     * @param string $type
     * @return null|\stdClass
     */

    public function getClasses( string $type )
    {
        switch ( $type )
        {

            case MVC_TYPE_MODEL:
                return $this->models->getAll();
                break;
            case MVC_TYPE_VIEW:
                return $this->views->getAll();
                break;
            case MVC_TYPE_CONTROLLER:
                return $this->controllers->getAll();
                break;
        }

        return null;
    }

    /**
     * @throws \Error
     */

    public function initialize()
    {

        $this->models = new Constructor( $this->getFilepath(MVC_TYPE_MODEL), $this->getNamespace(MVC_TYPE_MODEL) );
        $this->views = new Constructor( $this->getFilepath(MVC_TYPE_VIEW), $this->getNamespace(MVC_TYPE_VIEW) );
        $this->controllers = new Constructor( $this->getFilepath(MVC_TYPE_CONTROLLER), $this->getNamespace(MVC_TYPE_CONTROLLER) );

        $this->models->createAll();
        $this->views->createAll();
        $this->controllers->createAll();
    }

    /**
     * @return bool
     */

    public function test()
    {

        if( empty( $this->models->getAll() ) )
        {

            return false;
        }

        if( empty( $this->views->getAll() ) )
        {

            return false;
        }

        if( empty( $this->controllers->getAll() ) )
        {

            return false;
        }
    }

    /**
     * @param string $type
     * @return null|string
     */

    private function getFilepath( string $type )
    {

        switch( $type )
        {

            case MVC_TYPE_MODEL:
                return $this->filepath . COLOURSPACE_NAMESPACE_MODEL . '/';
                break;
            case MVC_TYPE_VIEW:
                return $this->filepath . COLOURSPACE_NAMESPACE_VIEW . '/';
                break;
            case MVC_TYPE_CONTROLLER:
                return $this->filepath . COLOURSPACE_NAMESPACE_CONTROLLER . '/';
                break;
        }

        return null;
    }

    /**
     * @param string $type
     * @return null|string
     */

    private function getNamespace( string $type )
    {

        switch ( $type )
        {

            case MVC_TYPE_MODEL:
                return $this->namespace . COLOURSPACE_NAMESPACE_MODEL . "\\";
                break;
            case MVC_TYPE_VIEW:
                return $this->namespace . COLOURSPACE_NAMESPACE_VIEW . "\\";
                break;
            case MVC_TYPE_CONTROLLER:
                return $this->namespace . COLOURSPACE_NAMESPACE_CONTROLLER . "\\";
                break;

        }

        return null;
    }
}