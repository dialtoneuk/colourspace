<?php
require_once( "vendor/autoload.php" );


/**
 * Written by Lewis 'mkultra2018' Lancaster
 * in 2018 (June to August)
 * =======================================
 */

use Colourspace\Application;
use Colourspace\Database\Connection;
use Colourspace\Container;
use Colourspace\Framework\FrontController;
use Colourspace\Framework\Session;
use Colourspace\Framework\Router;
use Colourspace\Framework\Util\Debug;

/**
 * Pre Checks
 * =======================================
 */

if (empty( $_SERVER["DOCUMENT_ROOT"] ) )
    $_SERVER["DOCUMENT_ROOT"] = getcwd();

if( version_compare(PHP_VERSION, '7.0.0') == -1 )
    die('Please upgrade to PHP 7.0.0+ to run this web-application. Your current PHP version is ' . PHP_VERSION );

if( php_sapi_name() === 'cli' )
    die('Please run this web application through a web-server. You are currently running PHP from CLI');

/**
 * Globals
 * =======================================
 */

define("COLOURSPACE_ROOT", $_SERVER["DOCUMENT_ROOT"] );
define("COLOURSPACE_DATABASE_CREDENTIALS", "/config/database_credentials.json");
define("COLOURSPACE_URL_ROOT", "/");

define("COLOURSPACE_MVC_ROOT", "/src/Framework/");

define("COLOURSPACE_GROUPS_ROOT", "/config/groups/");

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

define("FORM_ERROR_GENERAL", "general_error");
define("FORM_ERROR_INCORRECT", "incorrect_information");
define("FORM_ERROR_MISSING", "missing_information");

define("FORM_MESSAGE_SUCCESS", "success_message");
define("FORM_MESSAGE_INFO", "info_message");

define("DEBUG_ENABLED", true );
define("DEBUG_MESSAGES_FILE", '/config/debug/messages.json');
define("DEBUG_WRITE_FILE", true );

/**
 * Colourspace Initialization
 * =======================================
 */

try
{

    /**
     * Debug Timers
     * =======================
     */


    if( DEBUG_ENABLED )
    {

        //This will automatically allow all the debug methods in the application to function
        Debug::initialization();

        //Lets start a timer for the application process
        Debug::setStartTime('application');
    }

    /**
     * Initialization
     * =======================
     */

    $application = new Application();
    $application->connection = new Connection( true );

    if( $application->connection->test() == false )
        throw new Error("Failed connection test, check settings");

    $application->frontcontroller = new FrontController( true );

    if( $application->frontcontroller->test() == false )
        throw new Error("Failed to initiate front controller, check your files");

    $application->router = new Router( true );

    if( $application->router->test() == false )
        throw new Error('Router failed to initiate, check your router files');

    $application->session = new Session( false );
;
    Container::add("application", $application );
    Container::get('application')->session->initialize();

    if( DEBUG_ENABLED )
        Debug::setEndTime('application');

    /**
     * Flight
     * =======================
     */

    foreach( $application->router->live_routes as $url=>$payload )
    {

        Flight::route( $url, function( $route ) use ( $payload ){

            $request = Container::get('application')->frontcontroller->buildRequest( $route );

            if ( DEBUG_ENABLED )
                Debug::setStartTime('flight_route');

            $view = Container::get('application')->frontcontroller->process( $request, $payload );


            if ( empty( $view ) == false && is_array( $view ) )
                Flight::render( $view[0], $view[1] );
            else
                throw new Error("Unknown return type from view");

        }, true );
    }

    Flight::after('route', function()
    {

        if( DEBUG_ENABLED )
            Debug::setEndTime('flight_route' );
    });

    Flight::after('start', function()
    {
        if( DEBUG_ENABLED && DEBUG_WRITE_FILE )
            Debug::stashMessages();
    });

    //This is actually where the application starts
    Flight::start();
}
catch ( Error $error )
{

    die( print_r( $error ) );
}

