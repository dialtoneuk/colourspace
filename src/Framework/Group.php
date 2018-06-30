<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 22:55
 */

namespace Colourspace\Framework;

use Colourspace\Framework\Util\FileOperator;
use Illuminate\Support\Facades\File;

class Group
{

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
     * @return mixed
     */

    public function getGroup( $name )
    {

        return( $this->groups->$name );
    }

    /**
     * @param $name
     * @return bool
     */

    public function hasGroup( $name )
    {

        return( isset( $this->groups->$name ) );
    }

    /**
     * @param $name
     * @param bool $file_remove
     */

    public function deleteGroup( $name, $file_remove=true )
    {

        unset( $this->groups->$name );

        if( $file_remove )
            unlink( COLOURSPACE_ROOT .COLOURSPACE_GROUPS_ROOT . $name . ".json" );
    }

    /**
     * @throws \Error
     */

    private function crawlGroups()
    {

        $files = glob( COLOURSPACE_ROOT . COLOURSPACE_GROUPS_ROOT );


        foreach( $files as $key=>$file )
        {

            $file = $this->trim( $file );
            $file = new FileOperator( COLOURSPACE_ROOT . COLOURSPACE_GROUPS_ROOT . $file );

            if( $file->isJSON() == false )
                throw new \Error('Group file is incorrect');

            if( $file->isEmpty() )
                throw new \Error('Group file is empty');

            $name = $file->getBaseName();
            $this->groups->$name = $file->decodeJSON();
        }
    }


    /**
     * @return bool
     */

    private function exists()
    {

        if( file_exists( COLOURSPACE_ROOT . COLOURSPACE_GROUPS_ROOT ) == false )
            return false;

        if( is_file( COLOURSPACE_ROOT . COLOURSPACE_GROUPS_ROOT ) )
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