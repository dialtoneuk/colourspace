<?php
require_once( "vendor/autoload.php" );

/**
 * Written by Lewis 'mkultra2018'  Lancaster in 2018
 */

use Colourspace\Application;
use Colourspace\Database\Connection;
use Colourspace\Container;
use Colourspace\Framework\FrontController;
use Colourspace\Framework\Session;
use Colourspace\Framework\Router;

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
define("MVC_REQUEST_POST", "POST");
define("MVC_REQUEST_GET", "GET");
define("MVC_REQUEST_PUT", "PUT");
define("MVC_REQUEST_DELETE", "DELETE");

define('ROUTER_ROUTES', '/config/routes.json');

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

    /**
     * Create the connection for the database
     */

    //Create the database
    $application->connection = new Connection( true );
    //Test
    if( $application->connection->test() == false )
        throw new Error("Failed connection test, check settings");

    /**
     * Create the MVC Classes once the session has been initiated
     */

    //Create Front controller and initialize
    $application->frontcontroller = new FrontController( true );
    //Test
    if( $application->frontcontroller->test() == false )
        throw new Error("Failed to initiate front controller, check your files");

    /**
     * Now for the routing engine to read our routes
     */

    //Create the router class and added it to the application
    $application->router = new Router( true );
    //Test
    if( $application->router->test() == false )
        throw new Error('Router failed to initiate, check your router files');

    /**
     * Sessions come next
     */

    //Lets create the session, but we can't start it yet because the connection hasn't been made global yet.
    $application->session = new Session( false );
;
    //Once we have added the session to the global container, we can now invoke it and initialize our session.
    Container::add("application", $application );

    //Now we can initialize the session
    Container::get('application')->session->initialize();
}
catch ( Error $error )
{

    die( print_r( $error ) );
}
