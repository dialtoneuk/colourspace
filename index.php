<?php
require_once( "vendor/autoload.php" );

/**
 * Pre Checks
 * =======================================
 */

if (empty( $_SERVER["DOCUMENT_ROOT"] ) )
    $_SERVER["DOCUMENT_ROOT"] = getcwd();

if( version_compare(PHP_VERSION, '7.0.0') == -1 )
    die('Please upgrade to PHP 7.0.0+ to run this web application. Your current PHP version is ' . PHP_VERSION );

if( php_sapi_name() === 'cli' )
    die('Please run this web application through a web server. You are currently running PHP from CLI');

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

define("GOOGLE_ENABLED", false );
define("GOOGLE_SITE_KEY", "6LfIbAgUAAAAABzfN4j-MrX5ndXzjIb9jFNgg7Lv" );
define("GOOGLE_SITE_SECRET", "6LfIbAgUAAAAAKcKKopzftATinfo9vdmjgqzS77c" );

define("FLIGHT_JQUERY_FILE", "jquery-3.3.1.min.js");
define("FLIGHT_MODEL_OBJECT", true ); //Instead, convert the model payload into an object ( which is cleaner )
define("FLIGHT_MODEL_DEFINITION", "content" );
define("FLIGHT_SET_GLOBALS", true );

define("MVC_NAMESPACE", "Colourspace\\Framework\\");
define("MVC_NAMESPACE_MODELS", "Models");
define("MVC_NAMESPACE_VIEWS", "Views");
define("MVC_NAMESPACE_CONTROLLERS", "Controllers");
define("MVC_TYPE_MODEL", "model");
define("MVC_TYPE_VIEW", "view");
define("MVC_TYPE_CONTROLLER", "controller");
define("MVC_REQUEST_POST", "post");
define("MVC_REQUEST_GET", "get");
define("MVC_REQUEST_PUT", "put");
define("MVC_REQUEST_DELETE", "delete");
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

define("SCRIPT_BUILDER_ENABLED", true ); //It isnt recommended you turn this on unless your compiled.js for some reason is missing or you are developing.
define("SCRIPT_BUILDER_ROOT", "/assets/scripts/");
define("SCRIPT_BUILDER_FREQUENCY", 60 * 60 * 2); //Change the last digit for hours. Remove a "* 60" for minutes.
define("SCRIPT_BUILDER_COMPILED", "/assets/js/compiled.js");
define("SCRIPT_BUILDER_FORCED", true ) ;//Compiles a fresh build each request regardless of frequency setting.

define("COLLECTOR_DEFAULT_NAMESPACE", "Colourspace\\Framework\\");

define("COLOURS_OUTPUT_HEX", 1);
define("COLOURS_OUTPUT_RGB", 2);

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
        Debug::setStartTime('application');
    }

    /**
     * Initialization
     * =======================
     */

    $application = new Application();

    //This must be initiated and created first as some core functions require access to a database.
    $application->connection = new Connection( true );

    //We then run a test of the database connection. This simply pulls the database name from the database which will return an error if it fails
    if( $application->connection->test() == false )
        throw new Error("Failed connection test, check settings");

    //We then initiate the front controller. This loads all of our MVC classes into memory so there are no load times when we process the request. It is all live from memory.
    $application->frontcontroller = new FrontController( true );

    //We then test this by simply querying the stacks of Views, Models and Controllers and checking if any one of them are empty.
    if( $application->frontcontroller->test() == false )
        throw new Error("Failed to initiate front controller, check your files");

    //The router file is what is then fed into Flight. Our microframework for easy http routing ( read more up on this at flightphp.com ). All this really does is read from a json file
    //a list of arrays with a url of which to match and initiate this perticular set of MVC classes, and the prementioned MVC classes them selves. You can check out the file
    //for how this works.
    $application->router = new Router( true );

    //Same as the previous test, if we are empty. Error.
    if( $application->router->test() == false )
        throw new Error('Router failed to initiate, check your router files');

    //We then create the session class, but we do not initialize. Read below for more info on this.
    $application->session = new Session( false );

    //After everything is done, we globalize the application class to be accessible where ever we are in the web-application. Saving on CPU overhead and memory and time
    //when accessing these heavily used components.
    Container::add("application", $application );

    //After the container has been globalized. We can then initiate the session from invoking it directly inside the container. We do it this way because the
    //session class by default creates a table class so it can cross reference with a database to check things such as login states and the current
    //owner of the session. if they are logged in. The table class invokes the connection class to get a current active database connection.
    //So in order to initialize the session class, we first need to globalize the application and along with it our active database connection.
    //You should take this into account when working with the session class and to make sure when working with the database component that the application has been
    //initialized, and added to this global container.
    Container::get('application')->session->initialize();

    /**
     * Flight
     * =======================
     */

    foreach( $application->router->live_routes as $url=>$payload )
    {

        Flight::route( $url, function( $route ) use ( $payload ){

            $request = Container::get('application')->frontcontroller->buildRequest( $route );

            $view = Container::get('application')->frontcontroller->process( $request, $payload );

            if ( empty( $view ) == false && is_array( $view ) )
            {

                if( isset( $view['render'] ) == false )
                    throw new Error('No render');

                if( isset( $view['model'] ) == false )
                    throw new Error('No model');

                if( isset( $view['footer'] ) == false )
                    $view["footer"] = [];

                if( isset( $view['header'] ) == false )
                    $view["footer"] = [];

                $object = array_merge( $view['model'], [
                    "footer" => $view['footer'],
                    "header" => $view['header'],
                ]);

                if( FLIGHT_MODEL_OBJECT )
                    $object = json_decode( json_encode( $object ) );

                Flight::view()->set( FLIGHT_MODEL_DEFINITION , $object );

                if( FLIGHT_SET_GLOBALS )
                {

                    Flight::view()->set("url_root", COLOURSPACE_URL_ROOT );
                    Flight::view()->set("document_root", COLOURSPACE_ROOT );

                    if( DEBUG_ENABLED )
                        Flight::view()->set("debug_messages", Debug::getMessages() );
                }

                Flight::render( $view['render'] );
            }
            else
                Flight::redirect( COLOURSPACE_URL_ROOT );

        }, true );
    }

    Flight::before('start', function(){


        if ( DEBUG_ENABLED )
            Debug::setStartTime('flight_route');
    });

    Flight::after('start', function()
    {
        if( DEBUG_ENABLED  )
        {
            Debug::setEndTime('flight_route' );

            if( DEBUG_WRITE_FILE )
                Debug::stashMessages();
                Debug::stashTimers();
        }

    });

    Debug::message("Finished application loading");
    Debug::setEndTime('application');

    //This is actually where the application starts
    Flight::start();
}
catch ( Error $error )
{

    die( print_r( $error ) );
}

