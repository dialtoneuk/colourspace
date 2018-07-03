<?php
require_once( "vendor/autoload.php" );

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
 * Globals
 * =======================================
 */

define("COLOURSPACE_ROOT", $_SERVER["DOCUMENT_ROOT"] );
define("COLOURSPACE_URL_ROOT", "/");

define("ACCOUNT_PREFIX", "user");
define("ACCOUNT_DIGITS", 8);
define("ACCOUNT_RND_MIN", 1);
define("ACCOUNT_RND_MAX", 8);
define("ACCOUNT_PASSWORD_MIN", 8);
define("ACCOUNT_PASSWORD_STRICT", false );

define("GOOGLE_ENABLED", true );
define("GOOGLE_SITE_KEY", null );
define("GOOGLE_SITE_SECRET", null );

define("MVC_NAMESPACE", "Colourspace\\Framework\\");
define("MVC_NAMESPACE_MODELS", "Models");
define("MVC_NAMESPACE_VIEWS", "Views");
define("MVC_NAMESPACE_CONTROLLERS", "Controllers");
define("MVC_TYPE_MODEL", "model");
define("MVC_TYPE_VIEW", "view");
define("MVC_TYPE_CONTROLLER", "controller");
define("MVC_REQUEST_POST", "POST");
define("MVC_REQUEST_GET", "GET");
define("MVC_REQUEST_PUT", "PUT");
define("MVC_REQUEST_DELETE", "DELETE");
define('MVC_ROUTE_FILE', '/config/routes.json');
define("MVC_ROOT", "/src/Framework/");

define("FORM_ERROR_GENERAL", "general_error");
define("FORM_ERROR_INCORRECT", "incorrect_information");
define("FORM_ERROR_MISSING", "missing_information");
define("FORM_MESSAGE_SUCCESS", "success_message");
define("FORM_MESSAGE_INFO", "info_message");

define("FIELD_TYPE_INCREMENTS","increments");
define("FIELD_TYPE_STRING","string");
define("FIELD_TYPE_INT","integer");
define("FIELD_TYPE_PRIMARY","primary");
define("FIELD_TYPE_TIMESTAMP","timestamp");
define("FIELD_TYPE_DECIMAL","decimal");
define("FIELD_TYPE_JSON","json");
define("FIELD_TYPE_IPADDRESS","ipAddress");

define("TABLES_NAMESPACE", "Colourspace\\Framework\\Tables\\");
define("TABLES_ROOT", "src/Database/Tables/");

define("DATABASE_ENCRYPTION", false );
define("DATABSAE_ENCRYPTION_KEY", null ); //Replace null with a string of a key to not use a rand gen key.
define("DATABASE_CREDENTIALS", "/config/database_credentials.json");

define("GROUP_ROOT", "/config/groups/");
define("GROUP_DEFAULT", "default");

define("DEBUG_ENABLED", true );
define("DEBUG_WRITE_FILE", true );
define("DEBUG_MESSAGES_FILE", '/config/debug/messages.json');
define("DEBUG_TIMERS_FILE", '/config/debug/timers.json');

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

        Debug::message("Started");

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
                Flight::redirect( COLOURSPACE_URL_ROOT );

        }, true );
    }

    Flight::after('route', function()
    {

        if( DEBUG_ENABLED )
            Debug::setEndTime('flight_route' );
    });

    Flight::after('start', function()
    {
        if( DEBUG_ENABLED  )
        {
            Debug::message("Finished");
            Debug::setEndTime('application');

            if( DEBUG_WRITE_FILE )
                Debug::stashMessages();
        }

    });

    //This is actually where the application starts
    Flight::start();
}
catch ( Error $error )
{

    die( print_r( $error ) );
}

