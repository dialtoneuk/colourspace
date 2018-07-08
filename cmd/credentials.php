<?php
require_once "vendor/autoload.php";

define("CMD", true );

include_once "index.php";

echo( "Colourspace Credentials Creator \n");

try
{

    if( file_exists( COLOURSPACE_ROOT . "config/database_verification.json" ) == false )
        throw new Error("Verification file missing");

    echo( "- Getting base \n");
    $array = json_decode( file_get_contents( COLOURSPACE_ROOT . "config/database_verification.json" ), true );
    $inputs = [];
    echo( "- Getting user inputs \n");

    foreach( $array as $key=>$value )
    {

        echo( "Please enter: " . $key . "\n" );
        $inputs[ $key ] = readline($key . ": ");
    }

    echo( "- User inputs obtained \n");

    if( DATABASE_ENCRYPTION )
    {

        echo( "- Encryption Enabled \n");
        $opensll = new \Colourspace\Database\Util\OpenSSL();
        $key = $opensll->generateKey();
        $inputs = $opensll->encrypt( $inputs, $key, $opensll->iv(), true );
        echo( "- Array Encrypted \n");
    }

    echo( "- Writing to file \n");

    if( file_exists( COLOURSPACE_ROOT . DATABASE_CREDENTIALS ) )
        unlink( COLOURSPACE_ROOT . DATABASE_CREDENTIALS );

    file_put_contents( COLOURSPACE_ROOT . DATABASE_CREDENTIALS, json_encode( $inputs ) );
}
catch( Error $error )
{

    echo( "[Critical Error] : " . $error->getMessage() . "\n" );
}

echo("Finished");