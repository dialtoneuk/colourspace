<?php
require_once "vendor/autoload.php";

use Colourspace\Application;
use Colourspace\Database\Connection;
use Colourspace\Container;

//Check current root
if (empty( $_SERVER['DOCUMENT_ROOT'] ) )
{

    //If no document root use cwd instead
    $_SERVER['DOCUMENT_ROOT'] = getcwd();
}

//Globals
define("COLOURSPACE_ROOT", $_SERVER['DOCUMENT_ROOT'] );
define("COLOURSPACE_DATABASE_CREDENTIALS", "/config/database_credentials.json");

define('LARAVEL_TYPE_INCREMENTS',"increments");
define('LARAVEL_TYPE_STRING',"string");
define('LARAVEL_TYPE_INT',"integer");
define('LARAVEL_TYPE_TIMESTAMP',"timestamp");


/**
 * Initialize
 */

try
{

    $application = new Application();

    //Create the database
    $application->connection = new Connection( true );

    if( $application->connection->test() == false )
        throw new Error('Failed connection test, check settings');

    //Add to global container
    Container::add('application', $application );
}
catch ( Error $error )
{

    die( print_r( $error ) );
}
