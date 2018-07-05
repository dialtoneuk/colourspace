<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 03/07/2018
 * Time: 16:35
 */

namespace Colourspace\Framework\Util;


class Format
{

    /**
     * @param null $time
     * @return false|string
     */

    public static function timestamp($time=null )
    {

        if( $time == null )
            $time = time();

        return( date('Y-m-d H:i:s',$time ) );
    }

    /**
     * @param string $text
     * @return string
     */

    public static function largeText( string $text )
    {

        return( base64_encode( $text ) );
    }

    /**
     * @param string $text
     * @return bool|string
     */

    public static function decodeLargeText( string $text )
    {

        return( base64_decode( $text ) );
    }

    /**
     * @param $salt
     * @param $password
     * @return string
     */

    public static function saltedPassword( $salt, $password )
    {

        return( sha1( $salt . $password ) );
    }
}