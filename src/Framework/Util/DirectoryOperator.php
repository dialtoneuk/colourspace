<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 03/07/2018
 * Time: 18:29
 */

namespace Colourspace\Framework\Util;


class DirectoryOperator
{

    /**
     * @var string
     */
    protected $path;
    /**
     * @var array
     */
    public $contents;

    /**
     * DirectoryOperator constructor.
     * @param $path
     * @param bool $auto_read
     * @throws \Error
     */

    public function __construct( $path, $auto_read = true )
    {

        if( file_exists( COLOURSPACE_ROOT . $path ) == false )
            throw new \Error("Directory does not exist");

        if( is_file( COLOURSPACE_ROOT . $path ) )
            throw new \Error("Path references file not directory");

        $this->path = $path;

        if( $auto_read )
            $this->read();
    }

    /**
     * @return mixed
     */

    public function get()
    {

        if( $this->hasContents() == false )
            $this->read();

        return( $this->contents );
    }

    /**
     * @return array|null
     */

    public function getDirs()
    {

        return( $this->scrape( true ) );
    }

    /**
     * @param string $extension
     * @return array
     */

    public function search( $extension=".js" )
    {

        if( $this->hasContents() == false )
            $this->read();

        $results = [];

        foreach( $this->contents as $path )
        {

            if( str_contains( $path, $extension ) )
                $results[] = $path;
        }

        return( $results );
    }


    public function read()
    {

        $this->contents = $this->scrape();
    }

    /**
     * @return bool
     */

    public function isEmpty()
    {

        if( empty( $this->scrape() ) )
            return true;

        return false;
    }


    /**
     * @return bool
     */

    public function hasDirs()
    {

        if( empty( $this->scrape( true ) ) )
            return false;

        return true;
    }

    /**
     * @return bool
     */

    private function hasContents()
    {

        return( empty( $this->contents ) );
    }

    /**
     * @param bool $dir_only
     * @return array|null
     */

    private function scrape( $dir_only=false )
    {

        if( $dir_only )
            $options = GLOB_ONLYDIR;
        else
            $options = null;

        $contents = glob( COLOURSPACE_ROOT . $this->path . "*", $options );

        if( empty( $contents ) )
            return null;

        return( $contents );
    }
}