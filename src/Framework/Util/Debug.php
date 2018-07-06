<?php
namespace Colourspace\Framework\Util;


class Debug
{

    protected static $objects;

    /**
     *
     */

    public static function initialization()
    {

        self::$objects = new \stdClass();
        self::$objects->timers = new \stdClass();
    }

    /**
     * @param string $message
     * @param bool $include_time
     * @throws \Error
     */

    public static function message( string $message, bool $include_time=true )
    {

        if( DEBUG_ENABLED == false )
            return;

        if( self::isInit() == false )
            self::initialization();

        if( isset( self::$objects->messages ) == false )
            self::$objects->messages = [];

        if( $include_time )
            $time = microtime();
        else
            $time = false;

        self::$objects->messages[] = [
            'message'   => $message,
            'time'      => $time
        ];
    }

    /**
     * @param $name
     * @param $time
     * @throws \Error
     */

    public static function setStartTime( $name, $time=null )
    {

        if( DEBUG_ENABLED == false )
            return;

        if( $time == null )
            $time = microtime( true );

        if( self::isInit() == false )
            throw new \Error('Please enable error debugging');

        if( isset( self::$objects->timers->$name ) )
        {

            if(isset( self::$objects->timers->$name["start"] ) )
                throw new \Error("Start time has already been set");
        }

        self::$objects->timers->$name = [
            "start" => $time
        ];
    }

    /**
     * @throws \Error
     */

    public static function stashMessages()
    {

        if( DEBUG_ENABLED == false )
            return;

        if( self::hasMessages() == false )
            return;


        if( file_exists( COLOURSPACE_ROOT . DEBUG_MESSAGES_FILE ) == false )
            self::checkDirectory();

        file_put_contents(COLOURSPACE_ROOT . DEBUG_MESSAGES_FILE, json_encode( self::getMessages(), JSON_PRETTY_PRINT ) );
    }

    /**
     * @throws \Error
     */

    public static function stashTimers()
    {

        if( DEBUG_ENABLED == false )
            return;

        if( self::hasTimers() == false )
            return;

        if( file_exists( COLOURSPACE_ROOT . DEBUG_TIMERS_FILE ) == false )
            self::checkDirectory();

        file_put_contents(COLOURSPACE_ROOT . DEBUG_TIMERS_FILE, json_encode( self::getTimers(), JSON_PRETTY_PRINT ) );
    }

    /**
     * @param $name
     * @param $time
     * @throws \Error
     */

    public static function setEndTime( $name, $time=null )
    {

        if( DEBUG_ENABLED == false )
            return;

        if( $time == null )
            $time = microtime( true );

        if( self::isInit() == false )
            throw new \Error('Please enable error debugging');

        if( isset( self::$objects->timers->$name ) )
        {

            if(isset( self::$objects->timers->$name["end"] ) )
                throw new \Error("End time has already been set");
        }
        else
            throw new \Error('Invalid timer');

        self::$objects->timers->$name['end'] = $time;
    }

    /**
     * @param $name
     * @return float
     * @throws \Error
     */

    public static function getDifference( $name )
    {

        if( isset( self::$objects->timers->$name ) == false )
            throw new \Error('Invalid timer');

        $times = self::$objects->timers->$name;

        return( $times['end'] - $times['start'] );
    }


    /**
     * @param $name
     * @return bool
     */

    public static function hasTimer( $name )
    {

        return( isset( self::$objects->timers->$name ) );
    }

    /**
     * @return mixed
     */

    public static function getMessages()
    {

        return( self::$objects->messages );
    }

    /**
     * @return mixed
     */

    public static function getTimers()
    {

        return( self::$objects->timers );
    }

    /**
     * @return bool
     */

    public static function hasMessages()
    {

        if( isset( self::$objects->messages ) == false )
            return false;

        if( empty( self::$objects->messages ) )
            return false;

        return true;
    }

    /**
     * @return bool
     */

    public static function hasTimers()
    {

        if( isset( self::$objects->timers ) == false )
            return false;

        if( empty( self::$objects->timers ) )
            return false;

        return true;
    }

    /**
     * @return bool
     */

    private static function isInit()
    {

        if( self::$objects instanceof \stdClass == false )
            return false;

        return true;
    }

    /**
     * @throws \Error
     */

    private static function checkDirectory()
    {

        $removed_filename = explode('/', DEBUG_MESSAGES_FILE );
        array_pop( $removed_filename );

        $filename = implode( "/", $removed_filename ) . "/";

        if( is_file( COLOURSPACE_ROOT . $filename ) )
        {

            throw new \Error('Returned path is not a directory');
        }


        if( file_exists( COLOURSPACE_ROOT . $filename ) == false )
            mkdir( COLOURSPACE_ROOT . $filename );
    }
}