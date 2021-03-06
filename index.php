<?php
require_once( "vendor/autoload.php" );

//<editor-fold defaultstate="collapsed" desc="PHP pre checks">

/**
 * PHP pre checks
 * =======================================
 */

if (empty( $_SERVER["DOCUMENT_ROOT"] ) )
    $root = getcwd();
else
    $root = $_SERVER["DOCUMENT_ROOT"];

if( substr( $root, -1 ) !== DIRECTORY_SEPARATOR )
    $root = $root . DIRECTORY_SEPARATOR;

if( version_compare(PHP_VERSION, '7.0.0') == -1 )
    die('Please upgrade to PHP 7.0.0+ to run this web application. Your current PHP version is ' . PHP_VERSION );

if( php_sapi_name() === 'cli' && defined( "CMD" ) == false )
    die('Please run this web application through your web browser. It wont work via the console! (Muh live programs)');

if( $forceinfo = false )
    if( $forceinfo )
        die("<pre>" . @shell_exec("php cmd/info.php") . "</pre>" );


/**
 * Written by Lewis 'mkultra2018' Lancaster
 * in 2018 (June to August)
 * =======================================
 */

//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="Namespaces">

use Colourspace\Application;
use Colourspace\Database\Connection;
use Colourspace\Container;
use Colourspace\Framework\FrontController;
use Colourspace\Framework\Session;
use Colourspace\Framework\Router;
use Colourspace\Framework\Util\Debug;
use Colourspace\Framework\Interfaces\ReturnsInterface;

//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="Application Settings">

/**
 * Application Settings
 * =======================================
 */

/**
 * Since all scripts you run via cli should be in the cmd folder, which should be just one folder up. We can simply extend the
 * document root to look down a level when dealing with CMD mode.
 */

define("COLOURSPACE_ROOT", $root );
define("COLOURSPACE_URL_ROOT", "/");

//User Accounts
define("ACCOUNT_PREFIX", "user");
define("ACCOUNT_DIGITS", 8);
define("ACCOUNT_RND_MIN", 1);
define("ACCOUNT_RND_MAX", 8);
define("ACCOUNT_PASSWORD_MIN", 8);
define("ACCOUNT_PASSWORD_STRICT", false );

//Tracks
define("TRACK_PRIVACY_PUBLIC", "public");
define("TRACK_PRIVACY_PRIVATE", "private");
define("TRACK_PRIVACY_PERSONAL", "personal");
define("TRACK_PREFIX", "track");
define("TRACK_NAME_MAXLENGTH", 64);
define("TRACK_DIGITS", 12);
define("TRACK_RND_MIN", 0);
define("TRACK_RND_MAX", 9);

//Global Upload Settings
define("UPLOADS_TEMPORARY_DIRECTORY", "files/temp/");
define("UPLOADS_POST_KEY", "track");
define("UPLOADS_ERROR_NOT_FOUND", 1 );
define("UPLOADS_ERROR_FILENAME", 2 );
define("UPLOADS_ERROR_EXTENSION", 3 );
define("UPLOADS_ERROR_TOO_LARGE", 4 );
define("UPLOADS_ERROR_CANCELLED", 5 );
define("UPLOADS_WAVEFORMS_LOCAL", false );

define("FFMPEG_FOLDER","bin/");

//Amazon
define("AMAZON_BUCKET_URL", "https://s3.eu-west-2.amazonaws.com/colourspace/");
define("AMAZON_CREDENTIALS_FILE", "config/framework/aws.json");
define("AMAZON_S3_BUCKET", "colourspace");
define("AMAZON_LOCATION_US_WEST", "us-west-1");
define("AMAZON_LOCATION_US_WEST_2", "us-west-2");
define("AMAZON_LOCATION_US_EAST", "us-east-1");
define("AMAZON_LOCATION_US_EAST_2", "us-east-2");
define("AMAZON_LOCATION_CA_CENTRAL", "ca-central-1");
define("AMAZON_LOCATION_EU_WEST", "eu-west-1");
define("AMAZON_LOCATION_EU_WEST_2", "eu-west-2");
define("AMAZON_LOCATION_EU_CENTRAL", "eu-central-1");

//Google
define("GOOGLE_ENABLED", true );
define("GOOGLE_SITE_KEY", "6LfIbAgUAAAAABzfN4j-MrX5ndXzjIb9jFNgg7Lv" );
define("GOOGLE_SITE_SECRET", "6LfIbAgUAAAAAKcKKopzftATinfo9vdmjgqzS77c" );

//Flight
define("FLIGHT_JQUERY_FILE", "jquery-3.3.1.min.js");
define("FLIGHT_MODEL_OBJECT", true ); //Instead, convert the model payload into an object ( which is cleaner )
define("FLIGHT_MODEL_DEFINITION", "content" );
define("FLIGHT_SET_GLOBALS", true );

//MVC
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
define('MVC_ROUTE_FILE', 'config/framework/routes.json');
define("MVC_ROOT", "src/Framework/");

//Form
define("FORM_ERROR_GENERAL", "general_error");
define("FORM_ERROR_INCORRECT", "incorrect_information");
define("FORM_ERROR_MISSING", "missing_information");
define("FORM_MESSAGE_SUCCESS", "success_message");
define("FORM_MESSAGE_INFO", "info_message");

//Resource combiner

define("RESOURCE_COMBINER_ROOT", "config/");
define("RESOURCE_COMBINER_CHMOD", true );
define("RESOURCE_COMBINER_CHMOD_PERM", 0755 );
define("RESOURCE_COMBINER_PRETTY", true );
define("RESOURCE_COMBINER_FILEPATH", "config/resources" );

//Database Fields for tables
define("FIELD_TYPE_INCREMENTS","increments");
define("FIELD_TYPE_STRING","string");
define("FIELD_TYPE_INT","integer");
define("FIELD_TYPE_PRIMARY","primary");
define("FIELD_TYPE_TIMESTAMP","timestamp");
define("FIELD_TYPE_DECIMAL","decimal");
define("FIELD_TYPE_JSON","json");
define("FIELD_TYPE_IPADDRESS","ipAddress");

//Tables
define("TABLES_NAMESPACE", "Colourspace\\Database\\Tables\\");
define("TABLES_ROOT", "src/Database/Tables/");

//Database Settings
define("DATABASE_ENCRYPTION", false);
define("DATABSAE_ENCRYPTION_KEY", null ); //Replace null with a string of a key to not use a rand gen key.
define("DATABASE_CREDENTIALS", "config/database/credentials.json");
define("DATABASE_MAP", "config/database/map.json");

//Groups
define("GROUP_ROOT", "/config/groups/");
define("GROUP_DEFAULT", "default");
define("GROUPS_FLAG_MAXLENGTH", "uploadmaxlength");
define("GROUPS_FLAG_MAXSIZE", "uploadmaxsize");
define("GROUPS_FLAG_LOSSLESS", "lossless");

//Stream audio codec types
define("STREAMS_MP3", "mp3");
define("STREAMS_FLAC", "flac");
define("STREAMS_OGG", "ogg");
define("STREAMS_WAV", "wav");

//Debugging Options
define("DEBUG_ENABLED", true );
define("DEBUG_WRITE_FILE", true );
define("DEBUG_MESSAGES_FILE", 'config/debug/messages.json');
define("DEBUG_TIMERS_FILE", 'config/debug/timers.json');

//Javascript Builder
define("SCRIPT_BUILDER_ENABLED", true ); //It isnt recommended you turn this on unless your compiled.js for some reason is missing or you are developing.
define("SCRIPT_BUILDER_ROOT", "assets/scripts/");
define("SCRIPT_BUILDER_FREQUENCY", 60 * 60 * 2); //Change the last digit for hours. Remove a "* 60" for minutes.
define("SCRIPT_BUILDER_COMPILED", "assets/js/compiled.js");
define("SCRIPT_BUILDER_FORCED", true ) ;//Compiles a fresh build each request regardless of frequency setting.

//Misc
define("COLLECTOR_DEFAULT_NAMESPACE", "Colourspace\\Framework\\");

//Colours
define("COLOURS_OUTPUT_HEX", 1);
define("COLOURS_OUTPUT_RGB", 2);

//</editor-fold>

//<editor-fold desc="Initialization">

/**
 * Colourspace Initialization
 * =======================================
 */

if( defined( "CMD" ) == false )
{

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

            Debug::message("Request initiated");
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

                if( $view instanceof ReturnsInterface == false )
                    throw new Error("View must return a valid class which implements the return interface");

                $view->process();
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

                Debug::message("Request Complete");
                Debug::setEndTime('flight_route' );

                if( DEBUG_WRITE_FILE )
                {

                    Debug::stashMessages();
                    Debug::stashTimers();
                }

            }

        });

        Debug::message("Finished application loading");
        Debug::setEndTime('application');

        //This is actually where the application starts
        Flight::start();
    }
    catch ( Error $error )
    {

        //TODO: Advanced Error Screen
        die( print_r( $error ) );
    }
}
else
{
    echo("-> HEADS UP! <- \n");
    echo( "[ !Application quit because CMD is defined! ] \n\n");
}

//</editor-fold>
