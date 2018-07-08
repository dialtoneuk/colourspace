<?php
require_once "vendor/autoload.php";

define("CMD", true );

include_once "index.php";

echo( "Colourspace Connection Deploy \n");

try
{

    if( empty( $argv ) )
        throw new Error("Please enter arguments");

    if( file_exists( COLOURSPACE_ROOT . "config/database_verification.json" ) == false )
        throw new Error("Verification file missing");

    $result = [];

    foreach( $argv as $argument )
    {

        if( $argv[0] == $argument )
            continue;

        $explode = explode("=", $argument );

        if( count( $explode ) !== 2 )
            $result[ $explode[0] ] = null;
        else
            $result[ $explode[0] ] = $explode[1];
    }

    echo( "- Getting base \n");

    $array = json_decode( file_get_contents( COLOURSPACE_ROOT . "config/database_verification.json" ), true );

    echo( "- Checking user inputs \n");

    if( count( $result ) !== count( $array ) )
        throw new Error("miscount");

    foreach( $array as $key=>$value )
    {

        if( isset( $result[ $key ] ) == false )
            throw new Error("Missing key: " . $key );
    }

    echo( "- User inputs okay \n");

    if( DATABASE_ENCRYPTION )
    {

        echo( "- Encryption Enabled \n");

        $opensll = new \Colourspace\Database\Util\OpenSSL();
        $key = $opensll->generateKey();

        $result = $opensll->encrypt( $result, $key, $opensll->iv(), true );

        echo( "- Array Encrypted \n");
    }

    echo( "- Writing to file \n");

    if( file_exists( COLOURSPACE_ROOT . DATABASE_CREDENTIALS ) )
        unlink( COLOURSPACE_ROOT . DATABASE_CREDENTIALS );

    file_put_contents( COLOURSPACE_ROOT . DATABASE_CREDENTIALS, json_encode( $result ) );
}
catch( Error $error )
{

    echo( "Critical Error:" . $error->getMessage() );
}

echo("Complete!");