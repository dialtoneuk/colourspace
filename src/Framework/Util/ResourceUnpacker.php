<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 06/07/2018
 * Time: 16:15
 */

namespace Colourspace\Framework\Util;


class ResourceUnpacker
{

    /**
     * @var null|string
     */

    protected $filepath;

    /**
     * ResourceUnpacker constructor.
     * @param null $filepath
     * @throws \Error
     */

    public function __construct( $filepath=null )
    {

        if( $filepath == null )
            $filepath = RESOURCE_COMBINER_FILEPATH;

        if( file_exists( COLOURSPACE_ROOT . $filepath ) == false )
            throw new \Error("File does not exist " . COLOURSPACE_ROOT . $filepath);

        $this->filepath = $filepath;
    }

    /**
     * @throws \Error
     */

    public function process()
    {

        $resources = $this->get();

        if( empty( $resources ) )
            throw new \Error("Resources are empty");

        if( defined("CMD") )
            echo( "      Total Packed Json Objects: " . count( $resources ) . "\n");

        foreach( $resources as $path=>$contents )
        {

            if( defined("CMD") )
                echo( "  --> Upacking File: " . $path . "\n");

            if( file_exists( COLOURSPACE_ROOT . $path ) )
                continue;

            $directory = $this->getDirectory( $path );

            if( file_exists( COLOURSPACE_ROOT . $directory ) == false )
                $this->createFolder( $directory );

            if( RESOURCE_COMBINER_PRETTY )
                $this->createFile( $path, json_encode( $contents, JSON_PRETTY_PRINT ) );
            else
                $this->createFile( $path, json_encode( $contents ) );

            if( RESOURCE_COMBINER_CHMOD )
                chmod( COLOURSPACE_ROOT . $path, RESOURCE_COMBINER_CHMOD_PERM );
        }
    }

    /**
     * @return mixed
     * @throws \Error
     */

    public function get()
    {

        return( $this->read() );
    }

    /**
     * @param string $contents
     * @param bool $decode
     * @param bool $array
     * @return mixed
     */

    public function unpackString( string $contents, $decode=true, $array=true )
    {

        if( $decode )
            return( json_decode( Format::decodeLargeText( $contents ), $array ) );
        else
            return( json_decode(  $contents , $array ) );
    }

    /**
     * @param $filepath
     * @param string $contents
     * @throws \Error
     */

    private function createFile( $filepath, string $contents )
    {


        if( is_dir( COLOURSPACE_ROOT . $filepath ) )
            throw new \Error("Not a file");

        if( file_exists( COLOURSPACE_ROOT . $filepath ) )
            throw new \Error("File already exists");

        file_put_contents( COLOURSPACE_ROOT . $filepath, $contents );

        if( RESOURCE_COMBINER_CHMOD )
            chmod( COLOURSPACE_ROOT . $filepath, RESOURCE_COMBINER_CHMOD_PERM );
    }

    /**
     * @param $directory
     * @throws \Error
     */

    private function createFolder( $directory )
    {

        if( is_file( COLOURSPACE_ROOT . $directory ) )
            throw new \Error("Not a folder");

        if( file_exists( COLOURSPACE_ROOT . $directory ) )
            throw new \Error("Folder already exists");

        mkdir( COLOURSPACE_ROOT . $directory );

        if( RESOURCE_COMBINER_CHMOD )
            chmod( COLOURSPACE_ROOT . $directory, RESOURCE_COMBINER_CHMOD_PERM );
    }

    /**
     * @param $filepath
     * @return string
     */

    private function getDirectory( $filepath )
    {



        $exploded = explode( "/", $filepath );
        array_pop( $exploded );

        return( implode( "/", $exploded ) );
    }

    /**
     * @param bool $decode
     * @param bool $array
     * @return mixed
     * @throws \Error
     */

    private function read( $decode=true, $array=true )
    {

        $contents = Format::decodeLargeText( file_get_contents( COLOURSPACE_ROOT . $this->filepath ) );

        if( empty( $contents ) )
            throw new \Error("Empty contents");

        return( json_decode( $contents, $array ) );
    }
}