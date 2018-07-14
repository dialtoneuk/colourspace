<?php
require_once "vendor/autoload.php";

use Colourspace\Application;
use Colourspace\Database\Connection;
use Colourspace\Framework\Session;
use Colourspace\Container;

define("CMD", true );

include_once "index.php";

echo( "Colourspace Connection Tester \n");

try {

    echo("- Creating application \n");
    $application = new Application();
    echo("- Creating connection \n");
    $application->connection = new Connection(true);
    echo("- Connection created \n");
    echo("- Testing connection \n");

    if ( @$application->connection->test() == false)
        die(" --> Failed\n");
    else
        echo(" --> Passed\n");

    echo("- Testing table \n");
    $application->session = new Session( false );
    Container::add("application", $application );

    try
    {

        $application->session->initialize( false );

        if( $application->session->all()->isEmpty() )
            echo(" --> Passed\n");
        else
            echo(" --> Passed\n");
    }
    catch ( Error $error )
    {

        die(" --> Failed\n");
    }

    echo("- Cleaning up\n");
    Container::remove('application');

}
catch ( Error $error )
{

    echo( "[Critical Error] : " . $error->getMessage() . "\n" );
}

echo("Finished");