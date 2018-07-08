<?php
require_once "vendor/autoload.php";

use Colourspace\Application;
use Colourspace\Database\Connection;
use Colourspace\Database\Migrator;
use Colourspace\Container;

define("CMD", true );

include_once "index.php";

echo( "Colourspace Migrator \n");

try
{

    echo( "- Creating application \n");
    $application = new Application();
    echo( "- Creating connection \n");
    $application->connection = new Connection( true );
    echo( "- Testing Creating connection \n");

    if( $application->connection ->test() == false )
        throw new Error("Database test failed, cannot connect probably");

    Container::add("application", $application );
    echo( " --> Passed\n");
    echo( "- Creating migrator \n");
    $migrator = new Migrator();
    echo( "- Processing migrator \n");
    $migrator->process();
    echo( "- Complete \n");
}
catch ( Error $error )
{

    echo( "[Critical Error] : " . $error->getMessage() . "\n" );
}

echo("Finished");