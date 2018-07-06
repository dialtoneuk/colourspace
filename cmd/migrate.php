<?php
require_once "vendor/autoload.php";

use Colourspace\Application;
use Colourspace\Database\Connection;
use Colourspace\Database\Migrator;
use Colourspace\Container;

define("CMD", true );

include_once "index.php";

echo( "Colourspace Database Migrator \n");

try
{

    echo( "- Creating application \n");

    $application = new Application();

    echo( "- Creating connection \n");

    $application->connection = new Connection( true );

    if( $application->connection ->test() == false )
        throw new Error("Database test failed, cannot connect probably");

    Container::add("application", $application );

    echo( "- Passed Test \n");

    $migrator = new Migrator();

    echo( "- Processing migrator \n");

    $migrator->process();
}
catch ( Error $error )
{
    echo( print_r( $error ));
}

echo("Complete!");