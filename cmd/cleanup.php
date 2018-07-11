<?php
require_once "vendor/autoload.php";

use Colourspace\Application;
use Colourspace\Database\Connection;
use Colourspace\Database\Migrator;
use Colourspace\Container;

define("CMD", true );

include_once "index.php";

echo( "Colourspace Connection Returner \n");

try {

    echo("- Getting minutes");

    if( count( $argv ) !== 1 )
        $minutes = $argv[1];
    else
        $minutes = 5;

    echo("\n --> " . $minutes );

    echo("\n- Getting files in temp folder");

    $files = glob(COLOURSPACE_ROOT . UPLOADS_TEMPORARY_DIRECTORY . "*" );

    $count = 0;

    foreach( $files as $file )
    {

        if( is_dir( $file ) )
            continue;

        //10 minutes
        if(  filemtime ( $file ) > time() - ( 60 * $minutes ) )
        {
            echo("\n --> Skipping file: " . $file . " ( since too new )");
            continue;
        }

        echo( "\n --> Removing file: " . $file );

        if(@unlink( $file ) == false )
            echo( "  \n[Error] Couldnt remove file: " . $file );

        $count++;
    }

    echo("\n- Completed. Deleted total: " . $count . "\n" );

    echo("- Getting files in converted folder");

    $files = glob(COLOURSPACE_ROOT . "files/converted/" . "*" );

    $count2 = 0;

    foreach( $files as $file )
    {

        if( is_dir( $file ) )
            continue;

        if(  filemtime ( $file ) > time() - ( 60 * $minutes ) )
        {
            echo("\n --> Skipping file: " . $file . " ( since too new )");
            continue;
        }

        echo( "\n --> Removing file: " . $file );

        if(@unlink( $file ) == false )
            echo( "  \n[Error] Couldnt remove file: " . $file );

        $count2++;
    }

    echo("\n- Completed. Deleted total: " . $count2 );

    echo("\n- All files removed. Total removed: " . ( $count + $count2 ) );
}
catch ( Error $error )
{

    echo( "[Critical Error] : " . $error->getMessage() . "\n" );
}

echo("\nFinished");