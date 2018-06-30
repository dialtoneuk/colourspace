<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 00:24
 */
namespace Colourspace\Framework;

use Colourspace\Framework\Util\Factory;

class FrontController
{

    /**
     * @var Factory
     */
    protected $models;
    /**
     * @var Factory
     */
    protected $controllers;
    /**
     * @var Factory
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

        if ( $filepath== null )
            $this->filepath = COLOURSPACE_MVC_ROOT;
        else
            $this->filepath = $filepath;

        if( $auto_initialize == true )
            $this->initialize();
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
     * @return mixed|null
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
     * @return Factory|null
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

        $this->models = new Factory( $this->getFilepath(MVC_TYPE_MODEL), $this->getNamespace(MVC_TYPE_MODEL) );
        $this->views = new Factory( $this->getFilepath(MVC_TYPE_VIEW), $this->getNamespace(MVC_TYPE_VIEW) );
        $this->controllers = new Factory( $this->getFilepath(MVC_TYPE_CONTROLLER), $this->getNamespace(MVC_TYPE_CONTROLLER) );

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