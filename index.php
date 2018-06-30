<?php
require_once "vendor/autoload.php";

use Colourspace\Application;
use Colourspace\Database\Connection;
use Colourspace\Container;
use Colourspace\Framework\FrontController;

//Check current root
if (empty( $_SERVER["DOCUMENT_ROOT"] ) )
{

    //If no document root use cwd instead
    $_SERVER["DOCUMENT_ROOT"] = getcwd();
}

//Globals
define("COLOURSPACE_ROOT", $_SERVER["DOCUMENT_ROOT"] );
define("COLOURSPACE_DATABASE_CREDENTIALS", "/config/database_credentials.json");

define("COLOURSPACE_MVC_ROOT", "/src/Framework/");

define("COLOURSPACE_NAMESPACE", "Colourspace\\Framework\\");
define("COLOURSPACE_NAMESPACE_MODEL", "Models");
define("COLOURSPACE_NAMESPACE_VIEW", "Views");
define("COLOURSPACE_NAMESPACE_CONTROLLER", "Controllers");

define("MVC_TYPE_MODEL", "model");
define("MVC_TYPE_VIEW", "view");
define("MVC_TYPE_CONTROLLER", "controller");

define("LARAVEL_TYPE_INCREMENTS","increments");
define("LARAVEL_TYPE_STRING","string");
define("LARAVEL_TYPE_INT","integer");
define("LARAVEL_TYPE_TIMESTAMP","timestamp");


/**
 * Initialize
 */

try
{

    $application = new Application();
    //Create the database
    $application->connection = new Connection( true );
    //Test
    if( $application->connection->test() == false )
        throw new Error("Failed connection test, check settings");
    //Create Front controller and initialize
    $application->frontcontroller = new FrontController( true );
    //Test
    if( $application->frontcontroller->test() == false )
        throw new Error("Failed to initiate front controller, check your files");

    //Add to global container
    Container::add("application", $application );
}
catch ( Error $error )
{

    die( print_r( $error ) );
}
