<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 15:41
 */

namespace Colourspace\Framework\Util;


use function GuzzleHttp\Psr7\mimetype_from_filename;

class FileOperator
{

    protected $path;
    public $contents;

    /**
     * FileOperator constructor.
     * @param $path
     * @param bool $auto_read
     * @throws \Error
     */

    public function __construct( $path, $auto_read = true )
    {

        if( file_exists( COLOURSPACE_ROOT . $path ) == false )
            throw new \Error('File does not exist: ' . COLOURSPACE_ROOT . $path );

        if( is_dir( COLOURSPACE_ROOT . $path ) )
            throw new \Error('File operator can only operate files');

        $this->path = $path;

        if( $auto_read )
            $this->read();
    }

    /**
     * @return bool
     */

    public function isEmpty()
    {

        if( $this->hasContents() == false )
            $this->read();

        return $this->hasContents();
    }

    /**
     * @return bool
     */

    public function isJSON()
    {

        if( $this->hasContents() == false )
            $this->read();

        json_decode( $this->contents );

        if( json_last_error() !== JSON_ERROR_NONE )
            return false;

        return true;
    }

    /**
     * @param bool $array
     * @return mixed
     */

    public function decodeJSON( $array = true )
    {

        if( $this->hasContents() == false )
            $this->read();

        return json_decode( $this->contents, $array );
    }

    /**
     * Appends to a file
     *
     * @param $data
     * @throws \Error
     */

    public function append( $data )
    {

        $handle = fopen( COLOURSPACE_ROOT . $this->path, 'a' );

        if( $handle == false )
            throw new \Error('Unable to open file, probably due to permissions error');

        fwrite( $handle, $data );
        fclose( $handle );
    }

    /**
     * @param $data
     */

    public function write( $data )
    {

        file_put_contents( COLOURSPACE_ROOT . $this->path, $data );
    }

    /**
     * Gets the name of the file, alone with out the extension
     *
     * @return mixed
     */

    public function getBaseName()
    {

        $exploded = explode("/", $this->path );
        $file = end( $exploded );
        $filename = explode('.', $file );

        return( $filename[0] );
    }

    /**
     * @return bool
     */

    private function hasContents()
    {

        return( empty( $this->contents ) );
    }

    /**
     * Reads
     */

    private function read()
    {

        $this->contents = file_get_contents( COLOURSPACE_ROOT . $this->path );
    }

    /**
     * @param $filepath
     * @param array $extensions
     * @return bool
     * @throws \Error
     */

    public static function checkExtension( $filepath, $extensions=["mp3"] )
    {

        if( file_exists( COLOURSPACE_ROOT . $filepath ) == false )
            throw new \Error("File must exist");

        $file_parts = pathinfo(COLOURSPACE_ROOT . $filepath);

        foreach( $extensions as $extension )
        {

            if(  $file_parts["extension"] == $extension )
                return true;
        }

        return false;
    }

    /**
     * @param $filepath
     * @param array $mimes
     * @return bool
     * @throws \Error
     */

    public static function checkMimeType($filepath, $mimes=["mp3"] )
    {

        if( file_exists( COLOURSPACE_ROOT . $filepath ) == false )
            throw new \Error("File must exist");

        $mimetype = mimetype_from_filename(COLOURSPACE_ROOT . $filepath);

        foreach($mimes as $mime )
        {

            if( $mimetype == $mime )
                return true;
        }

        return false;
    }
}