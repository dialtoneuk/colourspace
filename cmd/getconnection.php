<?php
require_once "vendor/autoload.php";

use Colourspace\Application;
use Colourspace\Database\Connection;
use Colourspace\Database\Migrator;
use Colourspace\Container;

define("CMD", true );

include_once "index.php";

echo( "Colourspace Connection Tester \n");

try {

    echo("- Creating application \n");

    $application = new Application();

    echo("- Creating connection \n");

    $application->connection = new Connection(false );

    echo("- Getting connection \n");

    print_r( $application->connection->settings );

}
catch ( Error $error )
{

    echo( "Critical Error:" . $error->getMessage() );
}

echo("Success");