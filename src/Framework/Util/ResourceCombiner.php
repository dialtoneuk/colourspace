<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 06/07/2018
 * Time: 10:45
 */

namespace Colourspace\Framework\Util;


class ResourceCombiner
{

    /**
     * @var DirectoryOperator
     */

    protected $directory;

    /**
     * ResourceCombiner constructor.
     * @param null $directory
     * @throws \Error
     */

    public function __construct( $directory=null )
    {

        if( $directory == null )
            $directory = RESOURCE_COMBINER_ROOT;

        if( file_exists( COLOURSPACE_ROOT . $directory ) == false )
            throw new \Error("Folder does not exist " . COLOURSPACE_ROOT . $directory);

        $this->directory = new DirectoryOperator( $directory );
    }

    /**
     * @return array|null
     * @throws \Error
     */

    public function build()
    {

        if( $this->directory->isEmpty() )
            return null;


        $finished = false;
        $last=false;
        $result = [];
        $dirs = [];

        while( $finished == false )
        {



            $files = $this->omitRoot( $this->directory->path(), $this->files() );

            if( empty( $files ) == false )
            {

                foreach( $files as $file )
                {

                    $operator = new FileOperator( $this->directory->path() . $file );

                    if( $operator->isJson() == false )
                        continue;

                    if( isset( $result[ $this->directory->path() . $file ] ) )
                        continue;

                    $result[ $this->directory->path() . $file ] = $operator->decodeJSON( true );
                }
            }

            if( $last == true )
            {

                $finished = true;
                break;
            }

            if( $this->directory->hasDirs() )
            {

                if( empty( $dirs ) == false )
                    $dirs = array_merge( $this->omitRoot( $this->directory->path(), $this->directory->getDirs() ), $dirs );
                else
                    $dirs = $this->omitRoot( $this->directory->path(), $this->directory->getDirs() );
            }

            foreach( $dirs as $key=>$dir )
            {

                $this->directory->setPath( RESOURCE_COMBINER_ROOT . $dir . "/" );

                unset( $dirs[ $key ] );

                if( empty( $dirs ) == false )
                    break;
                else
                {

                    $last = true;
                }
            }
        }

        return( $result );
    }

    /**
     * @param $build
     * @param null $filepath
     * @param bool $encode
     * @throws \Error
     */

    public function save( $build, $filepath=null, $encode=true )
    {

        if( $filepath == null )
            $filepath = RESOURCE_COMBINER_FILEPATH;

        if( is_array( $build ) == false && is_object( $build ) == false )
            throw new \Error("Should either be array or object");


        if( RESOURCE_COMBINER_PRETTY )
            $json = json_encode( $build, JSON_PRETTY_PRINT );
        else
            $json =  json_encode( $build );

        if( $encode )
            file_put_contents( COLOURSPACE_ROOT . $filepath, Format::largeText( $json ) );
        else
            file_put_contents( COLOURSPACE_ROOT . $filepath, $json );

        if( RESOURCE_COMBINER_CHMOD )
            chmod( COLOURSPACE_ROOT . $filepath, RESOURCE_COMBINER_CHMOD_PERM );
    }

    /**
     * @param $dirs
     * @return mixed
     * @throws \Error
     */

    public function scrapeDirectory( $dirs )
    {

        foreach( $dirs as $dir )
        {

            $directory = new DirectoryOperator( $this->directory->path() . $dir . "/" );

            if( $directory->isEmpty() )
                continue;

            return( $this->omitRoot( $this->directory->path() . $dir . "/" , $directory->search([".json"] ) ) );
        }
    }

    /**
     * @param $files
     * @return array
     * @throws \Error
     */

    public function scrapeFiles( $files )
    {

        $contents = [];

        foreach ( $files as $realfile )
        {

            $file = new FileOperator( $this->directory->path() . $realfile );

            if( $file->isJSON() == false )
                throw new \Error("File is not json");

            $contents[ $realfile ] = $file->decodeJSON( true );
        }

        return $contents;
    }

    /**
     * @param $dir
     * @return bool
     * @throws \Error
     */

    public function hasDirs( $dir )
    {

        $directory = new DirectoryOperator( $this->directory->path() . $dir . "/" );

        if( $directory->isEmpty() )
            return false;

        if( $directory->hasDirs() == false )
            return false;

        return true;
    }

    /**
     * @param $dir
     * @return array|bool|null
     * @throws \Error
     */

    public function getDirs( $dir )
    {

        $directory = new DirectoryOperator( $this->directory->path() . $dir . "/" );

        if( $directory->isEmpty() )
            return false;

        return( $directory->getDirs() );
    }

    /**
     * @return bool
     * @throws \Error
     */

    public function has()
    {

        if( empty( $this->files() ) )
            return false;

        return true;
    }

    /**
     * @return array
     * @throws \Error
     */

    public function files()
    {

        return( $this->directory->search( [".json"] ));
    }

    /**
     * @return array|null
     */

    public function folders()
    {

        return( $this->directory->getDirs() );
    }

    /**
     * @param $path
     * @param $contents
     * @return mixed
     */

    private function omitRoot( $path, $contents )
    {

        if( empty( $contents ) )
            return null;

        foreach( $contents as $key=>$value )
        {

            $contents[ $key ] = str_replace( COLOURSPACE_ROOT, "", $value );
            $contents[ $key ] = str_replace( $path, "", $contents[ $key ] );
        }

        return $contents;
    }

    /**
     * @param $result
     * @param $dir
     * @return bool
     */

    private function searchForDirectory( $result, $dir )
    {

        foreach( $result as $key=>$value )
        {

            if( preg_match("#".$dir."#", $key ) )
            {

                return true;
            }

            echo $key . "\n";
        }

        return false;
    }
}