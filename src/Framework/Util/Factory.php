<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 00:25
 */
namespace Colourspace\Framework\Util;

class Factory
{

    private $objects;
    private $file_path;
    private $namespace;

    /**
     * Factory constructor.
     * @param $file_path
     * @param $namespace
     * @throws \Error
     */

    public function __construct( $file_path, $namespace )
    {

        $this->objects = new \stdClass();

        if ( file_exists( COLOURSPACE_ROOT . $file_path ) == false || is_dir( COLOURSPACE_ROOT . $file_path ) == false )
            throw new \Error('Root filepath is invalid');

        $this->file_path = COLOURSPACE_ROOT . $file_path;
    }

    /**
     * @return null|\stdClass
     * @throws \Error
     */

    public function createAll()
    {

        $files = $this->crawl();

        if ( empty( $files ) )
            return null;

        if( $this->check( $files ) == false )
            throw new \Error('Either one or more classes do not exist');

        foreach ( $files as $file )
        {

            $namespace = $this->build( $file );
            $class = new $namespace;

            $file = strtolower( $file );

            if( isset( $this->objects->$file ) )
            {

                if( $this->objects->$file === $class )
                    continue;
            }

            $this->objects->$file = $class;
        }

        return $this->objects;
    }

    /**
     * @param $class_name
     * @return mixed
     * @throws \Error
     */

    public function createSingular( $class_name )
    {

        if( class_exists( $this->namespace . $class_name ) == false )
            throw new \Error('Class does not exist');

        $namespace = $this->build( $class_name );
        $class_name = strtolower( $class_name );

        $this->objects->$class_name = new $namespace;

        return $this->objects->$class_name;
    }

    /**
     * @return \stdClass
     */

    public function getAll()
    {

        return $this->objects;
    }

    /**
     * @param $class_name
     * @return mixed
     */

    public function get( $class_name )
    {

        return $this->objects->$class_name;
    }

    /**
     * @param $class_name
     * @return bool
     */

    public function exists( $class_name )
    {

        $files = $this->crawl();

        foreach( $files as $file )
        {

            if( strtolower( $file ) == strtolower( $class_name ) )
                return true;
        }

        return false;
    }

    /**
     * @param $class_name
     */

    public function remove( $class_name)
    {

        unset( $this->objects->$class_name );
    }

    /**
     * @param $class_name
     * @return bool
     */

    public function has( $class_name )
    {

        return( isset( $this->objects->$class_name ) );
    }

    /**
     * @return array
     */

    private function crawl()
    {

        $files = glob( $this->file_path . '*.php' );

        foreach ( $files as $key=>$file )
        {

            $files[ $key ] = $this->trim( $file );
        }

        return $files;
    }

    /**
     * @param array $class_names
     * @return bool
     * @throws \Error
     */

    private function check( array $class_names )
    {

        foreach ( $class_names as $class )
        {

            if ( is_string( $class ) == false )
                throw new \Error('Type Error');

            if ( class_exists( $this->namespace . $class ) == false )
                return false;
        }

        return true;
    }

    /**
     * @param $class_name
     * @return string
     */

    private function build( $class_name )
    {

        return( $this->namespace . $class_name );
    }

    /**
     * @param $filename
     * @return string
     */

    private function trim( $filename )
    {

        $filename = explode('.', $filename );
        unset( $filename[ key( end( $filename ) ) ] );

        return( implode('.', $filename ) );
    }
}