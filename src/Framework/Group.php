<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 22:55
 */

namespace Colourspace\Framework;

use Colourspace\Framework\Util\Colours;
use Colourspace\Framework\Util\FileOperator;
use Illuminate\Support\Facades\File;

class Group
{

    /**
     * @var \stdClass
     */

    protected $groups;

    /**
     * Group constructor.
     * @param bool $auto_initialize
     * @throws \Error
     */

    public function __construct( $auto_initialize=true )
    {

        $this->groups = new \stdClass();

        if( $auto_initialize )
            $this->initialize();
    }

    /**
     * @throws \Error
     */

    public function initialize()
    {

        if( $this->exists() == false )
            throw new \Error('Groups do not exist, check files and settings');

        $this->crawlGroups();

    }

    /**
     * @param $name
     * @param $flag
     * @return bool
     */

    public function hasFlag( $name, $flag )
    {
        $group = $this->get( $name );

        if( isset( $group["flags"][ $flag ] ) )
            return true;

        return false;
    }

    /**
     * Creates a new group and writes it to file. Takes base parameter which will use an existing group file as a template
     *
     * @param $name
     * @param string $description
     * @param string $colour
     * @param array $flags
     * @param string $base
     * @throws \Error
     */

    public function create( $name, $description="Musicians", $colour="", $flags=[], $base="default" )
    {

        if( $this->has( strtolower( $name ) ) )
            throw new \Error('Names must be unique');

        if( $this->has( $base ) == false )
            throw new \Error('Base is invalid');

        $base = $this->get( $base );
        $base['name'] = $name;
        $base['description'] = $description;

        if( $colour == null )
            $colour = Colours::generate( COLOURS_OUTPUT_HEX );

        $base['colour'] = $colour;

        if( empty( $flags ) == false )
        {

            foreach( $flags as $key=>$value )
                $base["flags"][ $key ] = $value;
        }

        $this->write( strtolower( $name ) . ".json", json_encode( $base ) );
        $this->groups->$name = $base;
    }

    /**
     * @param $name
     */

    public function delete( $name )
    {

        if( isset( $this->groups->$name ) )
            unset( $this->groups->$name );

        unlink(COLOURSPACE_ROOT . GROUP_ROOT . strtolower( $name ) . ".json" );
    }
    /**
     * @param $name
     * @return mixed
     */

    public function get($name )
    {

        return( $this->groups->$name );
    }

    /**
     * @param $name
     * @return bool
     */

    public function has($name )
    {

        return( isset( $this->groups->$name ) );
    }

    /**
     * @throws \Error
     */

    private function crawlGroups()
    {

        $files = glob( GROUP_ROOT );


        foreach( $files as $key=>$file )
        {

            $file = $this->trim( $file );
            $file = new FileOperator( GROUP_ROOT . $file );

            if( $file->isJSON() == false )
                throw new \Error('Group file is incorrect');

            if( $file->isEmpty() )
                throw new \Error('Group file is empty');

            $name = $file->getBaseName();
            $this->groups->$name = $file->decodeJSON();
        }
    }

    /**
     * @param $file
     * @param $data
     */

    private function write( $file, $data )
    {

        file_put_contents( COLOURSPACE_ROOT . GROUP_ROOT . $file, $data );
    }


    /**
     * @return bool
     */

    private function exists()
    {

        if( file_exists( COLOURSPACE_ROOT . GROUP_ROOT ) == false )
            return false;

        if( is_file( COLOURSPACE_ROOT . GROUP_ROOT ) )
            return false;

        return true;
    }

    /**
     * @param $filename
     * @return mixed
     */

    private function trim( $filename )
    {

        $exploded = explode("/", $filename );#
        $file = end( $exploded );

        return( $file);
    }

}